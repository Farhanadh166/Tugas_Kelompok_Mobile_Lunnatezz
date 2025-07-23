@extends('layouts.app')
@section('title', 'Daftar Pesanan')
@section('content')
<div class="section-header">
    <h1>Daftar Pesanan</h1>
    <div class="section-header-button ml-auto">
        <form method="GET" action="" class="form-inline">
            <select name="status" class="form-control mr-2" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('status')=='paid' ? 'selected' : '' }}>Paid</option>
                <option value="shipped" {{ request('status')=='shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="completed" {{ request('status')=='completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status')=='cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </form>
    </div>
</div>
<div class="section-body">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pelanggan</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pesanans as $i => $pesanan)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $pesanan->user->nama ?? '-' }}</td>
                            <td>{{ $pesanan->tanggal_pesanan }}</td>
                            <td>Rp {{ number_format($pesanan->total_harga,0,',','.') }}</td>
                            <td><span class="badge badge-{{ $pesanan->status == 'pending' ? 'warning' : ($pesanan->status == 'paid' ? 'info' : ($pesanan->status == 'shipped' ? 'primary' : ($pesanan->status == 'completed' ? 'success' : 'danger'))) }}">{{ ucfirst($pesanan->status) }}</span></td>
                            <td>
                                <a href="{{ route('pesanan.show', $pesanan) }}" class="btn btn-info btn-sm">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada pesanan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 