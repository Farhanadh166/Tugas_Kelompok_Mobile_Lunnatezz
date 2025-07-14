@extends('layouts.app')
@section('title', 'Detail Pembayaran')
@section('content')
<div class="section-header">
    <h1>Detail Pembayaran #{{ $pembayaran->id }}</h1>
    <a href="{{ route('pembayaran.index') }}" class="btn btn-light ml-auto">Kembali</a>
</div>
<div class="section-body">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><b>Info Pembayaran</b></div>
                <div class="card-body">
                    <p><b>ID Pembayaran:</b> #{{ $pembayaran->id }}</p>
                    <p><b>ID Pesanan:</b> <a href="{{ route('pesanan.show', $pembayaran->pesanan) }}">#{{ $pembayaran->pesanan->id }}</a></p>
                    <p><b>Tanggal Bayar:</b> {{ $pembayaran->tanggal_bayar ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y H:i') : '-' }}</p>
                    <p><b>Jumlah Bayar:</b> <strong style="color: #7c3aed;">Rp {{ number_format($pembayaran->jumlah_bayar,0,',','.') }}</strong></p>
                    <p><b>Status:</b> 
                        @if($pembayaran->status_bayar == 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($pembayaran->status_bayar == 'sukses')
                            <span class="badge badge-success">Sukses</span>
                        @else
                            <span class="badge badge-danger">Gagal</span>
                        @endif
                    </p>
                    <p><b>Tanggal Dibuat:</b> {{ $pembayaran->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><b>Info Pelanggan</b></div>
                <div class="card-body">
                    <p><b>Nama:</b> {{ $pembayaran->pesanan->user->nama ?? '-' }}</p>
                    <p><b>Email:</b> {{ $pembayaran->pesanan->user->email ?? '-' }}</p>
                    <p><b>Telepon:</b> {{ $pembayaran->pesanan->user->telepon ?? '-' }}</p>
                    <p><b>Alamat Kirim:</b> {{ $pembayaran->pesanan->alamat_kirim ?? '-' }}</p>
                    <p><b>Status Pesanan:</b> 
                        <span class="badge badge-{{ $pembayaran->pesanan->status == 'pending' ? 'warning' : ($pembayaran->pesanan->status == 'paid' ? 'info' : ($pembayaran->pesanan->status == 'shipped' ? 'primary' : ($pembayaran->pesanan->status == 'completed' ? 'success' : 'danger'))) }}">
                            {{ ucfirst($pembayaran->pesanan->status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    @if($pembayaran->bukti_bayar)
    <div class="card mt-4">
        <div class="card-header"><b>Bukti Pembayaran</b></div>
        <div class="card-body text-center">
            <img src="{{ url('/payment-proof/' . basename($pembayaran->bukti_bayar)) }}" alt="Bukti Pembayaran" style="max-width: 100%; max-height: 400px; border-radius: 8px;">
            <div class="mt-3">
                <a href="{{ url('/payment-proof/' . basename($pembayaran->bukti_bayar)) }}" target="_blank" class="btn btn-info">
                    <i class="fas fa-external-link-alt"></i> Lihat Gambar Penuh
                </a>
            </div>
        </div>
    </div>
    @endif
    
    <div class="card mt-4">
        <div class="card-header"><b>Update Status Pembayaran</b></div>
        <div class="card-body">
            <form method="POST" action="{{ route('pembayaran.update-status', $pembayaran) }}">
                @csrf
                <div class="form-group">
                    <label>Status Pembayaran</label>
                    <select name="status_bayar" class="form-control">
                        <option value="pending" {{ $pembayaran->status_bayar=='pending' ? 'selected' : '' }}>Pending</option>
                        <option value="sukses" {{ $pembayaran->status_bayar=='sukses' ? 'selected' : '' }}>Sukses</option>
                        <option value="gagal" {{ $pembayaran->status_bayar=='gagal' ? 'selected' : '' }}>Gagal</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Status
                </button>
            </form>
        </div>
    </div>
</div>
@endsection 