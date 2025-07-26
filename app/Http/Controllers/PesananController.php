<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $paymentMethod = $request->get('payment_method');
        
        $query = Pesanan::with(['user', 'pembayaran'])->orderBy('created_at', 'desc');
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($paymentMethod) {
            $query->where('metode_bayar', $paymentMethod);
        }
        
        $pesanans = $query->get();
        return view('pesanan.index', compact('pesanans', 'status', 'paymentMethod'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Pesanan $pesanan)
    {
        $pesanan->load(['user', 'detailPesanan.produk', 'pembayaran']);
        return view('pesanan.show', compact('pesanan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pesanan $pesanan)
    {
        // Untuk update status, bisa gunakan form di show
        return redirect()->route('pesanan.show', $pesanan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,shipped,completed,cancelled',
        ]);

        // Validasi alur status pesanan
        $allowedTransitions = $this->getAllowedStatusTransitions($pesanan->status);
        
        if (!in_array($request->status, $allowedTransitions)) {
            return redirect()->route('pesanan.show', $pesanan)
                ->with('error', 'Transisi status tidak valid. Status saat ini: ' . ucfirst($pesanan->status));
        }

        $oldStatus = $pesanan->status;
        $pesanan->status = $request->status;
        $pesanan->save();

        // Update status pembayaran jika status pesanan berubah
        if ($request->status === 'paid' && $pesanan->pembayaran) {
            $pesanan->pembayaran->update([
                'status_bayar' => 'sukses',
                'tanggal_bayar' => now()
            ]);
        } elseif ($request->status === 'cancelled' && $pesanan->pembayaran) {
            $pesanan->pembayaran->update([
                'status_bayar' => 'gagal'
            ]);
        }
        
        return redirect()->route('pesanan.show', $pesanan)
            ->with('success', 'Status pesanan berhasil diupdate dari ' . ucfirst($oldStatus) . ' ke ' . ucfirst($request->status) . '!');
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
