@extends('layouts.app')
@section('title', 'Detail Keranjang')
@section('content')
<div class="section-header">
    <h1>Detail Keranjang #{{ $keranjang->id }}</h1>
    <a href="{{ route('keranjang.index') }}" class="btn btn-light ml-auto">Kembali</a>
</div>
<div class="section-body">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><b>Info Pelanggan</b></div>
                <div class="card-body">
                    <p><b>Nama:</b> {{ $keranjang->user->nama ?? 'Tidak diketahui' }}</p>
                    <p><b>Email:</b> {{ $keranjang->user->email ?? '-' }}</p>
                    <p><b>Telepon:</b> {{ $keranjang->user->telepon ?? '-' }}</p>
                    <p><b>Alamat:</b> {{ $keranjang->user->alamat ?? '-' }}</p>
                    <p><b>Tanggal Dibuat:</b> {{ $keranjang->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><b>Item Keranjang</b></div>
                <div class="card-body">
                    @if($keranjang->itemKeranjang->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($keranjang->itemKeranjang as $i => $item)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->produk->gambar)
                                                    <img src="{{ asset($item->produk->gambar) }}" alt="Gambar" width="50" height="50" style="border-radius: 8px; margin-right: 10px;">
                                                @endif
                                                <div>
                                                    <strong>{{ $item->produk->nama ?? 'Produk tidak ditemukan' }}</strong><br>
                                                    <small class="text-muted">{{ $item->produk->kategori->nama ?? '-' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>Rp {{ number_format($item->produk->harga ?? 0, 0, ',', '.') }}</td>
                                        <td><span class="badge badge-primary">{{ $item->jumlah }}</span></td>
                                        <td><strong>Rp {{ number_format(($item->produk->harga ?? 0) * $item->jumlah, 0, ',', '.') }}</strong></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">Total Keranjang:</th>
                                        <th style="color: #7c3aed; font-size: 1.2rem;">Rp {{ number_format($total, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center" style="padding: 40px 20px;">
                            <i class="fas fa-shopping-bag" style="font-size: 3rem; color: #a78bfa; margin-bottom: 15px;"></i>
                            <h5 style="color: #7c3aed;">Keranjang Kosong</h5>
                            <p style="color: #6d28d9;">Tidak ada item dalam keranjang ini.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header"><b>Aksi</b></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <form action="{{ route('keranjang.destroy', $keranjang) }}" method="POST" onsubmit="return confirm('Yakin hapus keranjang ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Hapus Keranjang
                            </button>
                        </form>
                        <a href="{{ route('keranjang.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 