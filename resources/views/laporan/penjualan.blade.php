@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Laporan Penjualan</h1>
    <form method="GET" action="{{ route('laporan.penjualan') }}" class="form-inline mb-3">
        <div class="form-group mr-2">
            <label for="bulan" class="mr-2">Bulan</label>
            <select name="bulan" id="bulan" class="form-control">
                <option value="">Semua Bulan</option>
                @for($i=1; $i<=12; $i++)
                    <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                @endfor
            </select>
        </div>
        <div class="form-group mr-2">
            <label for="tahun" class="mr-2">Tahun</label>
            <select name="tahun" id="tahun" class="form-control">
                <option value="">Semua Tahun</option>
                @foreach($tahunList as $tahun)
                    <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary mr-2">Filter</button>
        <a href="{{ route('laporan.penjualan.pdf', request()->only('bulan','tahun')) }}" class="btn btn-danger" target="_blank">Export PDF</a>
    </form>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No Pesanan</th>
                <th>Pembeli</th>
                <th>Produk</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pesanan as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ date('d-m-Y', strtotime($item->tanggal_pesanan)) }}</td>
                <td>{{ $item->order_number }}</td>
                <td>{{ $item->user->nama ?? '-' }}</td>
                <td>
                    @foreach($item->detailPesanan as $detail)
                        {{ $detail->produk->nama ?? '-' }} ({{ $detail->jumlah }})<br>
                    @endforeach
                </td>
                <td>Rp{{ number_format($item->total_harga,0,',','.') }}</td>
                <td>{{ $item->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 