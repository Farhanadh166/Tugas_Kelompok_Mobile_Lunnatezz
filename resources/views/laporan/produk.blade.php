@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Laporan Produk</h1>
    <form method="GET" action="{{ route('laporan.produk') }}" class="form-inline mb-3">
        <div class="form-group mr-2">
            <label for="kategori_id" class="mr-2">Kategori</label>
            <select name="kategori_id" id="kategori_id" class="form-control">
                <option value="">Semua Kategori</option>
                @foreach($kategori as $kat)
                    <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary mr-2">Filter</button>
        <a href="{{ route('laporan.produk.pdf', request()->only('kategori_id')) }}" class="btn btn-danger" target="_blank">Export PDF</a>
    </form>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Jumlah Terjual</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produk as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->kategori->nama ?? '-' }}</td>
                <td>Rp{{ number_format($item->harga,0,',','.') }}</td>
                <td>{{ $item->stok }}</td>
                <td>{{ $item->jumlah_terjual }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 