@extends('layouts.app')
@section('title', 'Detail Pesanan')
@section('content')
<div class="section-header">
    <h1>Detail Pesanan</h1>
    <a href="{{ route('pesanan.index') }}" class="btn btn-light ml-auto">Kembali</a>
</div>
<div class="section-body">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><b>Info Pelanggan</b></div>
                <div class="card-body">
                    <p><b>Nama:</b> {{ $pesanan->user->nama ?? '-' }}</p>
                    <p><b>Email:</b> {{ $pesanan->user->email ?? '-' }}</p>
                    <p><b>Telepon:</b> {{ $pesanan->user->telepon ?? '-' }}</p>
                    <p><b>Alamat Kirim:</b> {{ $pesanan->alamat_kirim }}</p>
                    <p><b>Tanggal Pesanan:</b> {{ $pesanan->tanggal_pesanan }}</p>
                    <p><b>Status:</b> <span class="badge badge-{{ $pesanan->status == 'pending' ? 'warning' : ($pesanan->status == 'paid' ? 'info' : ($pesanan->status == 'shipped' ? 'primary' : ($pesanan->status == 'completed' ? 'success' : 'danger'))) }}">{{ ucfirst($pesanan->status) }}</span></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><b>Info Pembayaran</b></div>
                <div class="card-body">
                    @if($pesanan->pembayaran)
                        <p><b>Status:</b> {{ ucfirst($pesanan->pembayaran->status_bayar) }}</p>
                        <p><b>Tanggal Bayar:</b> {{ $pesanan->pembayaran->tanggal_bayar }}</p>
                        <p><b>Jumlah Bayar:</b> Rp {{ number_format($pesanan->pembayaran->jumlah_bayar,0,',','.') }}</p>
                        @if($pesanan->pembayaran->bukti_bayar)
                            <p><b>Bukti Bayar:</b><br><img src="{{ url('/payment-proof/' . basename($pesanan->pembayaran->bukti_bayar)) }}" alt="Bukti Bayar" width="180"></p>
                        @endif
                    @else
                        <p>Belum ada pembayaran.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-header"><b>Detail Produk</b></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pesanan->detailPesanan as $item)
                        <tr>
                            <td>{{ $item->produk->nama ?? '-' }}</td>
                            <td>Rp {{ number_format($item->harga,0,',','.') }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td>Rp {{ number_format($item->harga * $item->jumlah,0,',','.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total</th>
                            <th>Rp {{ number_format($pesanan->total_harga,0,',','.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-header"><b>Update Status Pesanan</b></div>
        <div class="card-body">
            <form method="POST" action="{{ route('pesanan.update', $pesanan) }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Status Pesanan</label>
                    <select name="status" class="form-control">
                        <option value="pending" {{ $pesanan->status=='pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $pesanan->status=='paid' ? 'selected' : '' }}>Paid</option>
                        <option value="shipped" {{ $pesanan->status=='shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="completed" {{ $pesanan->status=='completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $pesanan->status=='cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Status</button>
            </form>
        </div>
    </div>
</div>
@endsection 