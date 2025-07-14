<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\ItemKeranjang;
use Illuminate\Http\Request;

class KeranjangController extends Controller
{
    public function index()
    {
        // Ambil semua keranjang aktif (yang belum di-checkout)
        $keranjangs = Keranjang::with(['user', 'itemKeranjang.produk'])
            ->whereHas('itemKeranjang') // Hanya keranjang yang ada itemnya
            ->get()
            ->map(function ($keranjang) {
                // Hitung total harga keranjang
                $total = $keranjang->itemKeranjang->sum(function ($item) {
                    return $item->produk->harga * $item->jumlah;
                });
                
                $keranjang->total_harga = $total;
                return $keranjang;
            });

        return view('keranjang.index', compact('keranjangs'));
    }

    public function show(Keranjang $keranjang)
    {
        $keranjang->load(['user', 'itemKeranjang.produk']);
        
        // Hitung total harga
        $total = $keranjang->itemKeranjang->sum(function ($item) {
            return $item->produk->harga * $item->jumlah;
        });
        
        return view('keranjang.show', compact('keranjang', 'total'));
    }

    public function destroy(Keranjang $keranjang)
    {
        // Hapus semua item keranjang
        $keranjang->itemKeranjang()->delete();
        
        // Hapus keranjang
        $keranjang->delete();
        
        return redirect()->route('keranjang.index')
            ->with('success', 'Keranjang berhasil dihapus');
    }
} 