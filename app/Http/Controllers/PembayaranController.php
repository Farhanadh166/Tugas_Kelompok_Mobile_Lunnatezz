<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index()
    {
        $pembayarans = Pembayaran::with(['pesanan.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pembayaran.index', compact('pembayarans'));
    }

    public function show(Pembayaran $pembayaran)
    {
        $pembayaran->load(['pesanan.user', 'pesanan.detailPesanan.produk']);
        
        return view('pembayaran.show', compact('pembayaran'));
    }

    public function updateStatus(Request $request, Pembayaran $pembayaran)
    {
        $request->validate([
            'status_bayar' => 'required|in:pending,sukses,gagal'
        ]);

        $pembayaran->update([
            'status_bayar' => $request->status_bayar
        ]);

        // Jika pembayaran sukses, update status pesanan menjadi 'paid'
        if ($request->status_bayar == 'sukses') {
            $pembayaran->pesanan->update(['status' => 'paid']);
        }

        return redirect()->route('pembayaran.show', $pembayaran)
            ->with('success', 'Status pembayaran berhasil diupdate');
    }

    public function filter(Request $request)
    {
        $query = Pembayaran::with(['pesanan.user']);

        if ($request->status) {
            $query->where('status_bayar', $request->status);
        }

        if ($request->tanggal_dari) {
            $query->whereDate('tanggal_bayar', '>=', $request->tanggal_dari);
        }

        if ($request->tanggal_sampai) {
            $query->whereDate('tanggal_bayar', '<=', $request->tanggal_sampai);
        }

        $pembayarans = $query->orderBy('created_at', 'desc')->get();

        return view('pembayaran.index', compact('pembayarans'));
    }
} 