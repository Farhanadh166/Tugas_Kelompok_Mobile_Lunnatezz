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

        if ($request->status == 'sukses') {
            $pembayaran->tanggal_bayar = now();
            $pembayaran->pesanan->update(['status' => 'paid']);
        } else if (in_array($request->status, ['gagal', 'dibatalkan'])) {
             $pembayaran->pesanan->update(['status' => 'cancelled']);
        } else {
             $pembayaran->pesanan->update(['status' => 'pending']);
        }

        $pembayaran->save();

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran berhasil diperbarui.',
            'data' => $pembayaran
        ]);
    }
} 