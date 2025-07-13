@extends('layouts.app')
@section('title', 'Master Produk')
@section('content')
<div class="section-header">
    <h1>Master Produk</h1>
    <div class="section-header-button ml-auto">
        <a href="{{ route('produk.create') }}" class="btn btn-primary">+ Tambah Produk</a>
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
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Gambar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produks as $i => $produk)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $produk->nama }}</td>
                            <td>{{ $produk->kategori->nama ?? '-' }}</td>
                            <td>Rp {{ number_format($produk->harga,0,',','.') }}</td>
                            <td>{{ $produk->stok }}</td>
                            <td>
                                @if($produk->gambar_url)
                                    <img src="{{ $produk->gambar_url }}" alt="Gambar" width="60">
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('produk.edit', $produk) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('produk.destroy', $produk) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin hapus produk?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada produk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 