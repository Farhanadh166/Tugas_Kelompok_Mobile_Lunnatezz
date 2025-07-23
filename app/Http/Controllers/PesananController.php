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
        
        $query = Pesanan::with(['user'])->orderBy('created_at', 'desc');
        
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
        $pesanan->load(['user', 'detailPesanan.produk']);
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
        $pesanan->status = $request->status;
        $pesanan->save();
        return redirect()->route('pesanan.show', $pesanan)->with('success', 'Status pesanan berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
