<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function produk(Request $request)
    {
        $query = Produk::with('kategori')
            ->withCount(['detailPesanan as jumlah_terjual' => function($q) {
                $q->select(DB::raw('coalesce(sum(jumlah),0)'));
            }]);
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }
        $produk = $query->get();
        $kategori = \App\Models\Kategori::all();
        return view('laporan.produk', compact('produk', 'kategori'));
    }

    public function exportPdf(Request $request)
    {
        $query = Produk::with('kategori');
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }
        $produk = $query->get();
        $pdf = Pdf::loadView('laporan.produk_pdf', compact('produk'));
        return $pdf->download('laporan_produk.pdf');
    }

    public function penjualan(Request $request)
    {
        $query = \App\Models\Pesanan::with(['user', 'detailPesanan.produk']);
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_pesanan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_pesanan', $request->tahun);
        }
        $pesanan = $query->orderBy('tanggal_pesanan', 'desc')->get();
        // Ambil tahun unik dari data pesanan
        $tahunList = \App\Models\Pesanan::selectRaw('YEAR(tanggal_pesanan) as tahun')->distinct()->pluck('tahun');
        return view('laporan.penjualan', compact('pesanan', 'tahunList'));
    }

    public function exportPenjualanPdf(Request $request)
    {
        $query = \App\Models\Pesanan::with(['user', 'detailPesanan.produk']);
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_pesanan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_pesanan', $request->tahun);
        }
        $pesanan = $query->orderBy('tanggal_pesanan', 'desc')->get();
        $pdf = Pdf::loadView('laporan.penjualan_pdf', compact('pesanan'));
        return $pdf->download('laporan_penjualan.pdf');
    }
} 