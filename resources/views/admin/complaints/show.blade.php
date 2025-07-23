@extends('layouts.app')
@section('title', 'Detail Komplain')
@section('content')
<div class="section-header">
    <h1>Detail Komplain</h1>
</div>
<div class="section-body">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3"><b>Pelanggan:</b> {{ $complaint->user->name ?? '-' }} ({{ $complaint->user->email ?? '-' }})</h5>
                    <h6 class="mb-2"><b>Pesanan:</b> {{ $complaint->order->order_number ?? '-' }}</h6>
                    <div class="mb-2"><b>Alasan:</b> {{ $complaint->reason }}</div>
                    <div class="mb-2"><b>Deskripsi:</b> {{ $complaint->description }}</div>
                    @if($complaint->photo)
                        <div class="mb-2"><b>Foto Bukti:</b><br>
                            <img src="{{ asset('storage/'.$complaint->photo) }}" alt="Foto Komplain" style="max-width:220px; border-radius:8px; box-shadow:0 2px 8px #a78bfa33;">
                        </div>
                    @endif
                    <div class="mb-2">
                        <b>Status:</b> 
                        <span class="badge badge-{{ $complaint->status == 'pending' ? 'warning' : ($complaint->status == 'diterima' ? 'success' : 'danger') }}">
                            {{ ucfirst($complaint->status) }}
                        </span>
                    </div>
                    <div class="mb-4">
                        <b>Response Admin:</b><br>
                        <span>{{ $complaint->response ?? '-' }}</span>
                    </div>
                    <form method="POST" action="{{ route('admin.complaints.update', $complaint->id) }}">
                        @csrf
                        <div class="form-group">
                            <label>Status Komplain</label>
                            <select name="status" class="form-control" required>
                                <option value="pending" {{ $complaint->status=='pending' ? 'selected' : '' }}>Pending</option>
                                <option value="diterima" {{ $complaint->status=='diterima' ? 'selected' : '' }}>Diterima</option>
                                <option value="ditolak" {{ $complaint->status=='ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tanggapan Admin</label>
                            <textarea name="response" class="form-control" rows="3">{{ old('response', $complaint->response) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                        <a href="{{ route('admin.complaints.index') }}" class="btn btn-light">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 