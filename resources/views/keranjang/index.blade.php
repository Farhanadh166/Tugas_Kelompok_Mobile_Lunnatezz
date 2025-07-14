@extends('layouts.app')
@section('title', 'Keranjang Aktif')
@section('content')
<div class="section-header">
    <h1>Keranjang Aktif</h1>
    <div class="section-header-button ml-auto">
        <span class="badge badge-info">{{ $keranjangs->count() }} Keranjang Aktif</span>
    </div>
</div>
<div class="section-body">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if($keranjangs->count() > 0)
        <div class="row">
            @foreach($keranjangs as $keranjang)
            <div class="col-md-6 col-lg-4">
                <div class="card" style="border-radius: 16px; box-shadow: 0 4px 16px rgba(160,120,200,0.1);">
                    <div class="card-header" style="background: linear-gradient(90deg, #a78bfa 0%, #f3e8ff 100%); border-radius: 16px 16px 0 0;">
                        <h5 style="color: #7c3aed; margin: 0; font-weight: 700;">
                            <i class="fas fa-shopping-cart"></i> Keranjang #{{ $keranjang->id }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong style="color: #7c3aed;">Pelanggan:</strong><br>
                            <span>{{ $keranjang->user->nama ?? 'Tidak diketahui' }}</span><br>
                            <small class="text-muted">{{ $keranjang->user->email ?? '-' }}</small>
                        </div>
                        
                        <div class="mb-3">
                            <strong style="color: #7c3aed;">Item Produk:</strong><br>
                            <span class="badge badge-primary">{{ $keranjang->itemKeranjang->count() }} produk</span>
                        </div>
                        
                        <div class="mb-3">
                            <strong style="color: #7c3aed;">Total Nilai:</strong><br>
                            <span style="font-size: 1.2rem; font-weight: 700; color: #7c3aed;">
                                Rp {{ number_format($keranjang->total_harga, 0, ',', '.') }}
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <strong style="color: #7c3aed;">Tanggal Dibuat:</strong><br>
                            <small>{{ $keranjang->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('keranjang.show', $keranjang) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            <form action="{{ route('keranjang.destroy', $keranjang) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin hapus keranjang ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="card-body text-center" style="padding: 60px 20px;">
                <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #a78bfa; margin-bottom: 20px;"></i>
                <h4 style="color: #7c3aed; margin-bottom: 10px;">Tidak Ada Keranjang Aktif</h4>
                <p style="color: #6d28d9;">Saat ini tidak ada keranjang belanja yang aktif dari pelanggan.</p>
            </div>
        </div>
    @endif
</div>
@endsection 