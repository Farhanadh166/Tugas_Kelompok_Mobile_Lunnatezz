@extends('layouts.app')
@section('title', 'Daftar Komplain')
@section('content')
<div class="section-header">
    <h1>Daftar Komplain Pelanggan</h1>
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
                            <th>Pelanggan</th>
                            <th>Pesanan</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $i => $complaint)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $complaint->user->name ?? '-' }}</td>
                            <td>{{ $complaint->order->order_number ?? '-' }}</td>
                            <td>{{ $complaint->reason }}</td>
                            <td>
                                <span class="badge badge-{{ $complaint->status == 'pending' ? 'warning' : ($complaint->status == 'diterima' ? 'success' : 'danger') }}">
                                    {{ ucfirst($complaint->status) }}
                                </span>
                            </td>
                            <td>{{ $complaint->created_at->format('d-m-Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="btn btn-info btn-sm">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada komplain.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 