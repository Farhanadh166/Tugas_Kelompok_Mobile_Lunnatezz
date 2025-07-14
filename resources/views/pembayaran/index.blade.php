@extends('layouts.app')
@section('title', 'Daftar Pembayaran')
@section('content')
<div class="section-header">
    <h1>Daftar Pembayaran</h1>
    <div class="section-header-button ml-auto">
        <form method="GET" action="{{ route('pembayaran.filter') }}" class="form-inline">
            <select name="status" class="form-control mr-2" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                <option value="sukses" {{ request('status')=='sukses' ? 'selected' : '' }}>Sukses</option>
                <option value="gagal" {{ request('status')=='gagal' ? 'selected' : '' }}>Gagal</option>
            </select>
            <input type="date" name="tanggal_dari" class="form-control mr-2" placeholder="Dari Tanggal" value="{{ request('tanggal_dari') }}">
            <input type="date" name="tanggal_sampai" class="form-control mr-2" placeholder="Sampai Tanggal" value="{{ request('tanggal_sampai') }}">
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>
</div>
<div class="section-body">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Tanggal Bayar</th>
                            <th>Jumlah Bayar</th>
                            <th>Status</th>
                            <th>Bukti Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembayarans as $i => $pembayaran)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>
                                <a href="{{ route('pesanan.show', $pembayaran->pesanan) }}" class="text-primary">
                                    #{{ $pembayaran->pesanan->id }}
                                </a>
                            </td>
                            <td>{{ $pembayaran->pesanan->user->nama ?? '-' }}</td>
                            <td>{{ $pembayaran->tanggal_bayar ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y H:i') : '-' }}</td>
                            <td>Rp {{ number_format($pembayaran->jumlah_bayar,0,',','.') }}</td>
                            <td>
                                @if($pembayaran->status_bayar == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($pembayaran->status_bayar == 'sukses')
                                    <span class="badge badge-success">Sukses</span>
                                @else
                                    <span class="badge badge-danger">Gagal</span>
                                @endif
                            </td>
                            <td>
                                @if($pembayaran->bukti_bayar)
                                    <a href="{{ url('/payment-proof/' . basename($pembayaran->bukti_bayar)) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('pembayaran.show', $pembayaran) }}" class="btn btn-info btn-sm">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada pembayaran.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 