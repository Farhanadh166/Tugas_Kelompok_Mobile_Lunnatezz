<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Get payment methods (hanya transfer manual)
     */
    public function methods()
    {
        $methods = [
            [
                'id' => 'transfer',
                'name' => 'Transfer Bank',
                'description' => 'Transfer ke rekening bank kami',
                'fee' => 0,
                'icon' => '🏦'
            ],
            [
                'id' => 'cod',
                'name' => 'Cash on Delivery (COD)',
                'description' => 'Bayar saat terima barang',
                'fee' => 20000,
                'icon' => '💵'
            ],
            [
                'id' => 'qris',
                'name' => 'QRIS (DANA, OVO, Gopay, dll)',
                'description' => 'Scan QRIS untuk membayar dengan e-wallet apa saja.',
                'fee' => 0,
                'icon' => '🔳'
            ]
        ];

        return response()->json([
            'status' => true,
            'message' => 'Metode pembayaran berhasil diambil',
            'data' => $methods
        ]);
    }

    /**
     * Get bank account info
     */
    public function bankInfo()
    {
        $accounts = [
            [
                'bank' => 'BCA',
                'account_number' => '1234567890',
                'account_name' => 'PT. Toko Online',
                'logo' => 'bca.png'
            ],
            [
                'bank' => 'Mandiri',
                'account_number' => '0987654321',
                'account_name' => 'PT. Toko Online',
                'logo' => 'mandiri.png'
            ]
        ];

        return response()->json([
            'status' => true,
            'message' => 'Informasi rekening bank berhasil diambil',
            'data' => $accounts
        ]);
    }

    /**
     * Upload payment proof
     */
    public function uploadProof(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = Auth::user();
        $order = Pesanan::where('order_number', $request->order_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        // Cek duplikasi bukti transfer
        $existingProof = Pembayaran::where('pesanan_id', $order->id)
            ->whereNotNull('bukti_bayar')->first();
        if ($existingProof) {
            return response()->json([
                'status' => false,
                'message' => 'Bukti transfer untuk order ini sudah diupload sebelumnya'
            ], 409);
        }

        // Ambil file dari request
        $file = $request->file('payment_proof');
        if (!$file) {
            \Log::error('File upload error: file not found in request');
            return response()->json([
                'status' => false,
                'message' => 'File tidak ditemukan dalam request'
            ], 400);
        }
        
        if (!$file->isValid()) {
            \Log::error('File upload error: file not valid', [
                'error' => $file->getError(),
                'error_message' => $file->getErrorMessage()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'File tidak valid: ' . $file->getErrorMessage()
            ], 400);
        }
        
        // Cek ukuran file dengan aman
        try {
            $fileSize = $file->getSize();
            if ($fileSize === false) {
                throw new \Exception('Tidak dapat membaca ukuran file');
            }
            if ($fileSize > 2 * 1024 * 1024) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ukuran file terlalu besar. Maksimal 2MB'
                ], 400);
            }
        } catch (\Exception $e) {
            \Log::error('File size check error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal memeriksa ukuran file: ' . $e->getMessage()
            ], 400);
        }
        // Upload bukti pembayaran ke storage/app/public/payment_proofs
        try {
            $filename = 'payment_proof_' . $order->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Simpan ke storage/app/public/payment_proofs
            $path = $file->storeAs('payment_proofs', $filename, 'public');
            
            if (!$path) {
                throw new \Exception('Gagal menyimpan file ke storage');
            }
            
        } catch (\Exception $e) {
            \Log::error('File upload error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan file: ' . $e->getMessage()
            ], 500);
        }

        // Logging
        \Log::info('Payment upload attempt', [
            'order_number' => $order->order_number,
            'file_size' => $file->getSize(),
            'user_id' => $user->id
        ]);

        // Update pembayaran
        $pembayaran = Pembayaran::where('pesanan_id', $order->id)->first();
        if ($pembayaran) {
            $pembayaran->update([
                'bukti_bayar' => $path, // Simpan path relatif dari storage
                'status_bayar' => 'pending', // Menunggu verifikasi admin
                'tanggal_bayar' => now() // Set tanggal pembayaran saat bukti diupload
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Bukti pembayaran berhasil diupload',
            'data' => [
                'order_id' => $order->order_number,
                'payment_proof_url' => url('/payment-proof/' . $filename)
            ]
        ]);
    }

    /**
     * Get payment status
     */
    public function status($orderId)
    {
        $user = Auth::user();
        $order = Pesanan::with(['pembayaran', 'detailPesanan.produk'])
            ->where('order_number', $orderId)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        $addressData = json_decode($order->alamat_kirim, true);
        $items = $order->detailPesanan->map(function ($item) {
            return [
                'produk_id' => $item->produk_id,
                'nama' => $item->produk->nama ?? '',
                'jumlah' => (int) $item->jumlah,
                'harga' => (int) $item->harga,
                'subtotal' => (int) ($item->jumlah * $item->harga),
                'gambar' => $item->produk->gambar_url ?? '',
            ];
        });
        $paymentData = null;
        if ($order->pembayaran) {
            $paymentData = [
                'status' => $order->pembayaran->status_bayar,
                'payment_date' => $order->pembayaran->tanggal_bayar,
                'proof_url' => $order->pembayaran->bukti_bayar ? url('/payment-proof/' . basename($order->pembayaran->bukti_bayar)) : null,
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Status pembayaran berhasil diambil',
            'data' => [
                'order_id' => $order->order_number,
                'status' => $order->pembayaran ? $order->pembayaran->status_bayar : $order->status,
                'order_status' => $order->status,
                'payment_method' => $order->metode_bayar,
                'total_amount' => (int) $order->total_harga,
                'shipping_cost' => 15000, // Default shipping cost
                'subtotal' => (int) ($order->total_harga - 15000),
                'notes' => $order->catatan,
                'address' => [
                    'name' => $addressData['name'] ?? '',
                    'phone' => $addressData['phone'] ?? '',
                    'address' => $addressData['address'] ?? '',
                    'city' => $addressData['city'] ?? '',
                    'province' => $addressData['province'] ?? '',
                    'postal_code' => $addressData['postal_code'] ?? '',
                ],
                'items' => $items,
                'payment' => $paymentData,
                'notes' => $order->catatan ?? null,
                'paid_at' => $order->pembayaran->tanggal_bayar ?? null,
                'verified_at' => $order->pembayaran->verified_at ?? null,
            ]
        ]);
    }
} 