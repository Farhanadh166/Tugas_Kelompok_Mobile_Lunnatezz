<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Keranjang;
use App\Models\Address;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponse;

class OrderController extends Controller
{
    use ApiResponse;
    /**
     * Create order from cart
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'address_id' => 'required|exists:addresses,id',
                'shipping_method' => 'required|string',
                'payment_method' => 'required|string',
                'notes' => 'nullable|string'
            ]);

            $user = Auth::user();

            // Check if address belongs to user
            $address = Address::where('user_id', $user->id)
                ->where('id', $request->address_id)
                ->first();

            if (!$address) {
                return response()->json([
                    'status' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }

            // Get user's cart
            $keranjang = Keranjang::where('user_id', $user->id)->first();
            
            if (!$keranjang) {
                return response()->json([
                    'status' => false,
                    'message' => 'Keranjang belanja kosong'
                ], 400);
            }

            $cartItems = $keranjang->itemsWithProduct()->get();
            
            if ($cartItems->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Keranjang belanja kosong'
                ], 400);
            }

            // Calculate shipping cost (simple calculation)
            $shippingCost = $this->calculateShippingCost($request->shipping_method);
            
            // Calculate payment method fee
            $paymentFee = $this->calculatePaymentFee($request->payment_method);
            
            // Calculate total
            $subtotal = $keranjang->total_amount;
            $totalAmount = $subtotal + $shippingCost + $paymentFee;

            DB::beginTransaction();

            try {
                // Create order
                $pesanan = Pesanan::create([
                    'user_id' => $user->id,
                    'tanggal_pesanan' => now(),
                    'total_harga' => $totalAmount,
                    'status' => 'pending',
                    'metode_bayar' => $request->payment_method,
                    'catatan' => $request->notes,
                    'alamat_kirim' => json_encode([
                        'name' => $address->name,
                        'phone' => $address->phone,
                        'address' => $address->address,
                        'city' => $address->city,
                        'province' => $address->province,
                        'postal_code' => $address->postal_code,
                    ])
                ]);
                // Set order_number setelah create
                $pesanan->order_number = 'ORD-' . date('Y') . '-' . str_pad($pesanan->id, 3, '0', STR_PAD_LEFT);
                $pesanan->save();

                // Create order details from cart items
                foreach ($cartItems as $item) {
                    DetailPesanan::create([
                        'pesanan_id' => $pesanan->id,
                        'produk_id' => $item->produk_id,
                        'jumlah' => $item->jumlah,
                        'harga' => $item->produk->harga
                    ]);

                    // KURANGI STOK PRODUK
                    $produk = $item->produk;
                    if ($produk->stok < $item->jumlah) {
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'message' => 'Stok produk ' . $produk->nama . ' tidak mencukupi.'
                        ], 400);
                    }
                    $produk->stok -= $item->jumlah;
                    $produk->save();
                }

                // BUAT DATA SHIPPING
                \App\Models\Shipping::create([
                    'pesanan_id' => $pesanan->id,
                    'shipping_method' => $request->shipping_method,
                    'shipping_cost' => $shippingCost,
                    'status' => 'pending'
                ]);

                // Create payment record
                $paymentStatus = $request->payment_method === 'cod' ? 'sukses' : 'pending';
                $paymentDate = $request->payment_method === 'cod' ? now() : null;
                
                $pembayaran = Pembayaran::create([
                    'pesanan_id' => $pesanan->id,
                    'jumlah_bayar' => $totalAmount,
                    'status_bayar' => $paymentStatus,
                    'tanggal_bayar' => $paymentDate
                ]);

                // Update order status for COD
                if ($request->payment_method === 'cod') {
                    $pesanan->update(['status' => 'paid']);
                }

                // Clear cart
                $keranjang->itemKeranjang()->delete();

                DB::commit();

                // Prepare response data
                $orderData = [
                    'order_id' => $pesanan->order_number, // WAJIB, format 'ORD-...'
                    'status' => $pesanan->status,
                    'created_at' => $pesanan->created_at ? $pesanan->created_at->toDateTimeString() : '',
                    'total_amount' => (int) $pesanan->total_harga,
                    'shipping_cost' => $shippingCost,
                    'payment_fee' => $paymentFee,
                    'subtotal' => (int) $subtotal,
                    'payment_method' => $pesanan->metode_bayar,
                    'notes' => $pesanan->catatan,
                    'address' => [
                        'id' => $address->id,
                        'name' => $address->name ?? '',
                        'phone' => $address->phone ?? '',
                        'address' => $address->address ?? '',
                        'city' => $address->city ?? '',
                        'province' => $address->province ?? '',
                        'postal_code' => $address->postal_code ?? '',
                    ],
                    'items' => $cartItems->map(function ($item) {
                        return [
                            'produk_id' => $item->produk_id,
                            'nama' => $item->produk->nama ?? '',
                            'jumlah' => (int) $item->jumlah,
                            'harga' => (int) $item->produk->harga,
                            'subtotal' => (int) ($item->jumlah * $item->produk->harga),
                            'gambar' => $item->produk->gambar_url ?? '',
                        ];
                    }),
                    'payment' => [
                        'status' => $pembayaran->status_bayar,
                        'payment_date' => $pembayaran->tanggal_bayar,
                        'proof_url' => $pembayaran->bukti_bayar ? url('/payment-proof/' . basename($pembayaran->bukti_bayar)) : null
                    ]
                ];

                $message = $request->payment_method === 'cod' 
                    ? 'Pesanan COD berhasil dibuat. Admin akan segera mengirim barang.' 
                    : 'Pesanan berhasil dibuat';

                return response()->json([
                    'status' => true,
                    'message' => $message,
                    'data' => $orderData
                ], 201);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's orders
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $orders = Pesanan::where('user_id', $user->id)
                ->with('detailPesanan')
                ->orderBy('created_at', 'desc')
                ->get();

            $data = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => 'ORD-' . date('Y', strtotime($order->created_at)) . '-' . str_pad($order->id, 3, '0', STR_PAD_LEFT),
                    'status' => $order->status,
                    'total_amount' => (int) $order->total_harga,
                    'created_at' => $order->created_at ? $order->created_at->toISOString() : '',
                    'items_count' => $order->detailPesanan->count()
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'Data pesanan berhasil diambil',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order detail
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            $order = Pesanan::where('user_id', $user->id)
                ->where('id', $id)
                ->with(['detailPesanan.produk', 'pembayaran'])
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

            $data = [
                'order_id' => $order->order_number,
                'status' => $order->status,
                'created_at' => $order->created_at ? $order->created_at->toISOString() : '',
                'total_amount' => (int) $order->total_harga,
                'shipping_cost' => 15000, // Default shipping cost
                'subtotal' => (int) ($order->total_harga - 15000),
                'payment_method' => $order->metode_bayar,
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
                'payment' => $order->pembayaran ? [
                    'status' => $order->pembayaran->status_bayar,
                    'payment_date' => $order->pembayaran->tanggal_bayar,
                    'proof_url' => $order->pembayaran->bukti_bayar ? url('/payment-proof/' . basename($order->pembayaran->bukti_bayar)) : null
                ] : null
            ];

            return response()->json([
                'status' => true,
                'message' => 'Data pesanan berhasil diambil',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order by order_number (string)
     */
    public function showByOrderNumber($order_number)
    {
        try {
            if (!preg_match('/^ORD-\\d{4}-\\d{3}$/', $order_number)) {
                return $this->errorResponse('Format order ID tidak valid', 400);
            }
            $user = Auth::user();
            $order = Pesanan::where('order_number', $order_number)
                ->where('user_id', $user->id)
                ->with(['detailPesanan.produk', 'pembayaran'])
                ->first();
            if (!$order) {
                return $this->errorResponse('Pesanan tidak ditemukan', 404);
            }
            $addressData = json_decode($order->alamat_kirim, true);
            $data = [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'total_amount' => (int) $order->total_harga,
                'shipping_cost' => 15000,
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
                'shipping_method' => $order->shipping->shipping_method ?? '',
                'payment_method' => $order->metode_bayar,
                'created_at' => $order->created_at ? $order->created_at->toISOString() : '',
                'items' => $order->detailPesanan->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'produk_id' => $item->produk_id,
                        'quantity' => (int) $item->jumlah,
                        'price' => (int) $item->harga,
                        'total' => (int) ($item->jumlah * $item->harga),
                        'produk' => [
                            'id' => $item->produk->id,
                            'nama' => $item->produk->nama ?? '',
                            'gambar' => $item->produk->gambar_url ?? '',
                        ]
                    ];
                }),
                'payment' => $order->pembayaran ? [
                    'id' => $order->pembayaran->id,
                    'status' => $order->pembayaran->status_bayar,
                    'amount' => $order->pembayaran->jumlah_bayar,
                    'payment_date' => $order->pembayaran->tanggal_bayar,
                    'proof_url' => $order->pembayaran->bukti_bayar ? url('/payment-proof/' . basename($order->pembayaran->bukti_bayar)) : null
                ] : null
            ];
            return $this->successResponse($data, 'Detail pesanan berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Calculate shipping cost
     */
    private function calculateShippingCost($method)
    {
        $costs = [
            'jne' => 15000,
            'jne_express' => 25000,
            'jnt' => 12000,
            'sicepat' => 13000
        ];

        return $costs[$method] ?? 15000;
    }

    /**
     * Calculate payment method fee
     */
    private function calculatePaymentFee($method)
    {
        $fees = [
            'transfer' => 0,
            'cod' => 20000
        ];

        return $fees[$method] ?? 0;
    }

    /**
     * Generate payment URL (untuk transfer manual)
     */
    private function generatePaymentUrl($pembayaran, $method)
    {
        // Untuk transfer manual, return null karena user akan upload bukti
        if ($method === 'transfer') {
            return null;
        }
        
        // Fallback untuk metode lain
        return "https://app.midtrans.com/snap/v2/vtweb/{$pembayaran->id}";
    }

    /**
     * Get status message
     */
    private function getStatusMessage($status)
    {
        $messages = [
            'pending' => 'Pesanan dibuat',
            'paid' => 'Pembayaran diterima',
            'shipped' => 'Pesanan dikirim',
            'completed' => 'Pesanan selesai',
            'cancelled' => 'Pesanan dibatalkan'
        ];

        return $messages[$status] ?? 'Status tidak diketahui';
    }

    /**
     * Get order history for logged-in user
     */
    public function orderHistory()
    {
        $user = Auth::user();
        $orders = Pesanan::where('user_id', $user->id)
            ->with(['detailPesanan.produk', 'shipping'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => 'ORD-' . date('Y', strtotime($order->created_at)) . '-' . str_pad($order->id, 3, '0', STR_PAD_LEFT),
                'status' => $order->status,
                'total_amount' => (int) $order->total_harga,
                'created_at' => $order->created_at ? $order->created_at->toISOString() : '',
                'shipping' => $order->shipping ? [
                    'status' => $order->shipping->status,
                    'tracking_number' => $order->shipping->tracking_number,
                    'shipping_method' => $order->shipping->shipping_method,
                    'shipping_cost' => $order->shipping->shipping_cost,
                    'shipped_at' => $order->shipping->shipped_at,
                    'delivered_at' => $order->shipping->delivered_at
                ] : null,
                'items' => $order->detailPesanan->map(function ($item) {
                    return [
                        'produk_id' => $item->produk_id,
                        'nama' => $item->produk->nama ?? '',
                        'gambar' => $item->produk->gambar_url ?? '',
                        'jumlah' => (int) $item->jumlah,
                        'harga' => (int) $item->harga
                    ];
                })
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Riwayat pesanan berhasil diambil',
            'data' => $data
        ]);
    }

    /**
     * Get order history grouped by status for the authenticated user
     */
    public function historyGrouped(Request $request)
    {
        $user = $request->user();
        $statuses = ['pending', 'paid', 'shipped', 'completed', 'cancelled'];
        $result = [];
        foreach ($statuses as $status) {
            $orders = $user->orders()
                ->where('status', $status)
                ->with(['detailPesanan.produk', 'shipping'])
                ->orderBy('created_at', 'desc')
                ->get();
            $result[$status] = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'total_amount' => (int) $order->total_harga,
                    'created_at' => $order->created_at ? $order->created_at->toISOString() : '',
                    'payment_method' => $order->metode_bayar,
                    'notes' => $order->catatan,
                    'shipping' => $order->shipping ? [
                        'status' => $order->shipping->status,
                        'tracking_number' => $order->shipping->tracking_number,
                        'shipping_method' => $order->shipping->shipping_method,
                        'shipping_cost' => $order->shipping->shipping_cost,
                        'shipped_at' => $order->shipping->shipped_at,
                        'delivered_at' => $order->shipping->delivered_at
                    ] : null,
                    'items' => $order->detailPesanan->map(function ($item) {
                        return [
                            'produk_id' => $item->produk_id,
                            'nama' => $item->produk->nama ?? '',
                            'gambar' => $item->produk->gambar_url ?? '',
                            'jumlah' => (int) $item->jumlah,
                            'harga' => (int) $item->harga
                        ];
                    })
                ];
            });
        }
        return response()->json([
            'success' => true,
            'message' => 'Riwayat pesanan berhasil diambil',
            'data' => $result
        ]);
    }

    /**
     * Konfirmasi pesanan sudah diterima oleh pelanggan
     */
    public function confirmReceived(Request $request, $id)
    {
        $user = $request->user();
        $order = \App\Models\Pesanan::where('id', $id)->where('user_id', $user->id)->first();
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan atau bukan milik Anda.'
            ], 404);
        }
        if ($order->status !== 'shipped') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan belum dalam status dikirim.'
            ], 400);
        }
        $order->status = 'completed';
        $order->save();
        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dikonfirmasi sudah diterima.',
            'data' => $order
        ]);
    }

    public function checkoutWithProof(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'address_id' => 'required|exists:addresses,id',
            'shipping_method' => 'required|string',
            'payment_method' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:produks,id',
            'items.*.qty' => 'required|integer|min:1',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string',
            'payment_notes' => 'nullable|string|max:500',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        \DB::beginTransaction();
        try {
            $year = date('Y');
            $lastOrder = \App\Models\Pesanan::whereYear('created_at', $year)->latest()->first();
            $sequence = $lastOrder ? intval(substr($lastOrder->order_number, -3)) + 1 : 1;
            $orderNumber = 'ORD-' . $year . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);

            $address = \App\Models\Address::find($request->address_id);
            $order = \App\Models\Pesanan::create([
                'order_number' => $orderNumber,
                'user_id' => auth()->id(),
                'tanggal_pesanan' => now(),
                'total_harga' => 0, // Akan diupdate setelah hitung item
                'status' => 'pending',
                'metode_bayar' => $request->payment_method,
                'catatan' => $request->notes,
                'alamat_kirim' => json_encode([
                    'name' => $address->name,
                    'phone' => $address->phone,
                    'address' => $address->address,
                    'city' => $address->city,
                    'province' => $address->province,
                    'postal_code' => $address->postal_code,
                ]),
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                $produk = \App\Models\Produk::find($item['product_id']);
                $subtotal = $produk->harga * $item['qty'];
                $order->detailPesanan()->create([
                    'produk_id' => $produk->id,
                    'jumlah' => $item['qty'],
                    'harga' => $produk->harga,
                ]);
                $total += $subtotal;
            }

            // Upload bukti pembayaran
            $file = $request->file('payment_proof');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('payment_proofs', $fileName, 'public');

            // Simpan pembayaran
            $order->pembayaran()->create([
                'jumlah_bayar' => $total,
                'status_bayar' => 'pending',
                'bukti_bayar' => $filePath,
                'catatan' => $request->payment_notes,
            ]);

            // Update total harga pesanan
            $order->total_harga = $total;
            $order->save();

            \DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Pesanan berhasil dibuat dan bukti pembayaran diupload. Menunggu verifikasi admin.',
                'data' => [
                    'order_number' => $order->order_number,
                    'order_status' => $order->status,
                    'payment_proof_status' => 'pending',
                ]
            ], 201);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage()
            ], 500);
        }
    }
}
