<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembayaran;

class AdminPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembayaran::with('pesanan.user')->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status != '') {
            $query->where('status_bayar', $request->status);
        }

        $pembayarans = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $pembayarans
        ]);
    }

    public function show($id)
    {
        $pembayaran = Pembayaran::with('pesanan.user', 'pesanan.detailPesanan.produk')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $pembayaran
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,sukses,gagal,dibatalkan',
        ]);

        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->status_bayar = $request->status;

        // Validasi alur status pesanan berdasarkan status pembayaran
        $pesanan = $pembayaran->pesanan;
        $allowedTransitions = $this->getAllowedStatusTransitions($pesanan->status);
        
        if ($request->status == 'sukses') {
            $pembayaran->tanggal_bayar = now();
            if (in_array('paid', $allowedTransitions)) {
                $pesanan->update(['status' => 'paid']);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat mengubah status pesanan ke PAID. Status saat ini: ' . ucfirst($pesanan->status)
                ], 400);
            }
        } else if (in_array($request->status, ['gagal', 'dibatalkan'])) {
            if (in_array('cancelled', $allowedTransitions)) {
                $pesanan->update(['status' => 'cancelled']);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat mengubah status pesanan ke CANCELLED. Status saat ini: ' . ucfirst($pesanan->status)
                ], 400);
            }
        } else {
            if (in_array('pending', $allowedTransitions)) {
                $pesanan->update(['status' => 'pending']);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat mengubah status pesanan ke PENDING. Status saat ini: ' . ucfirst($pesanan->status)
                ], 400);
            }
        }

        $pembayaran->save();

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran berhasil diperbarui.',
            'data' => $pembayaran
        ]);
    }

    /**
     * Get allowed status transitions based on current status
     */
    private function getAllowedStatusTransitions($currentStatus)
    {
        $transitions = [
            'pending' => ['paid', 'cancelled'],
            'paid' => ['shipped'],
            'shipped' => ['completed'],
            'completed' => [], // Final state
            'cancelled' => [], // Final state
        ];

        return $transitions[$currentStatus] ?? [];
    }
} 