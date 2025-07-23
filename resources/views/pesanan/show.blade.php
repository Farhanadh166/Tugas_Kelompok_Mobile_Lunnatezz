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
    
    @if($pesanan->metode_bayar == 'cod')
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Pesanan COD:</strong> Pembayaran akan dilakukan saat barang diterima. Status pembayaran langsung "Sukses" karena tidak perlu verifikasi admin.
        </div>
    @endif
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><b>Info Pelanggan</b></div>
                <div class="card-body">
                    <p><b>Order ID:</b> <strong>{{ $pesanan->order_number ?? 'ORD-' . date('Y') . '-' . str_pad($pesanan->id, 3, '0', STR_PAD_LEFT) }}</strong></p>
                    <p><b>Nama:</b> {{ $pesanan->user->nama ?? '-' }}</p>
                    <p><b>Email:</b> {{ $pesanan->user->email ?? '-' }}</p>
                    <p><b>Telepon:</b> {{ $pesanan->user->telepon ?? '-' }}</p>
                    <p><b>Alamat Kirim:</b> 
                        @php
                            $alamatData = json_decode($pesanan->alamat_kirim, true);
                        @endphp
                        @if($alamatData)
                            {{ $alamatData['name'] ?? '' }}<br>
                            {{ $alamatData['phone'] ?? '' }}<br>
                            {{ $alamatData['address'] ?? '' }}<br>
                            {{ $alamatData['city'] ?? '' }}, {{ $alamatData['province'] ?? '' }} {{ $alamatData['postal_code'] ?? '' }}
                        @else
                            {{ $pesanan->alamat_kirim }}
                        @endif
                    </p>
                    <p><b>Tanggal Pesanan:</b> {{ $pesanan->tanggal_pesanan }}</p>
                    <p><b>Status:</b> <span class="badge badge-{{ $pesanan->status == 'pending' ? 'warning' : ($pesanan->status == 'paid' ? 'info' : ($pesanan->status == 'shipped' ? 'primary' : ($pesanan->status == 'completed' ? 'success' : 'danger'))) }}">{{ ucfirst($pesanan->status) }}</span></p>
                    @if($pesanan->catatan)
                        <p><b>Catatan:</b> {{ $pesanan->catatan }}</p>
                    @endif
                    <p><b>Metode Pembayaran:</b> 
                        @if($pesanan->metode_bayar == 'cod')
                            <span class="badge badge-warning">💵 Cash on Delivery (COD)</span>
                        @elseif($pesanan->metode_bayar == 'transfer')
                            <span class="badge badge-info">🏦 Transfer Bank</span>
                        @else
                            <span class="badge badge-secondary">{{ $pesanan->metode_bayar ?? '-' }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><b>Info Pembayaran</b></div>
                <div class="card-body">
                    @if($pesanan->pembayaran)
                        <p><b>Status:</b> 
                            @if($pesanan->pembayaran->status_bayar == 'sukses')
                                <span class="badge badge-success">Sukses</span>
                            @elseif($pesanan->pembayaran->status_bayar == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-danger">Gagal</span>
                            @endif
                        </p>
                        <p><b>Tanggal Bayar:</b> {{ $pesanan->pembayaran->tanggal_bayar }}</p>
                        <p><b>Jumlah Bayar:</b> Rp {{ number_format($pesanan->pembayaran->jumlah_bayar,0,',','.') }}</p>
                        
                        @if($pesanan->metode_bayar == 'cod')
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>COD:</strong> Tidak ada bukti pembayaran karena pembayaran dilakukan saat terima barang.
                            </div>
                        @elseif($pesanan->pembayaran->bukti_bayar)
                            <p><b>Bukti Bayar:</b><br><img src="{{ url('/payment-proof/' . basename($pesanan->pembayaran->bukti_bayar)) }}" alt="Bukti Bayar" width="180"></p>
                        @else
                            <p><b>Bukti Bayar:</b> <span class="text-muted">Belum diupload</span></p>
                        @endif
                    @else
                        <p>Belum ada pembayaran.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    @if($pesanan->shipping)
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><b>Info Pengiriman</b></div>
                <div class="card-body">
                    <p><b>Metode Pengiriman:</b> {{ ucfirst($pesanan->shipping->shipping_method) }}</p>
                    <p><b>Status Pengiriman:</b> 
                        @if($pesanan->shipping->status == 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($pesanan->shipping->status == 'shipped')
                            <span class="badge badge-primary">Dikirim</span>
                        @elseif($pesanan->shipping->status == 'delivered')
                            <span class="badge badge-success">Terkirim</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($pesanan->shipping->status) }}</span>
                        @endif
                    </p>
                    <p><b>Biaya Pengiriman:</b> Rp {{ number_format($pesanan->shipping->shipping_cost,0,',','.') }}</p>
                    @if($pesanan->shipping->tracking_number)
                        <p><b>Nomor Resi:</b> {{ $pesanan->shipping->tracking_number }}</p>
                    @endif
                    @if($pesanan->shipping->shipped_at)
                        <p><b>Tanggal Dikirim:</b> {{ $pesanan->shipping->shipped_at }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
    
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
                        @if($pesanan->metode_bayar == 'cod')
                            <tr>
                                <th colspan="3" class="text-right">Subtotal</th>
                                <th>Rp {{ number_format($pesanan->total_harga - 35000,0,',','.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right">Ongkos Kirim</th>
                                <th>Rp 15.000</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right">Biaya COD</th>
                                <th>Rp 20.000</th>
                            </tr>
                        @else
                            <tr>
                                <th colspan="3" class="text-right">Subtotal</th>
                                <th>Rp {{ number_format($pesanan->total_harga - 15000,0,',','.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right">Ongkos Kirim</th>
                                <th>Rp 15.000</th>
                            </tr>
                        @endif
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