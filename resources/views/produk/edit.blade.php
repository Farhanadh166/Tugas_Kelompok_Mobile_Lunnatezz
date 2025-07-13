@extends('layouts.app')
@section('title', 'Edit Produk')
@section('content')
<div class="section-header">
    <h1>Edit Produk</h1>
</div>
<div class="section-body">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('produk.update', $produk) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="kategori_id" class="form-control @error('kategori_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}" {{ old('kategori_id', $produk->kategori_id) == $kategori->id ? 'selected' : '' }}>{{ $kategori->nama }}</option>
                                @endforeach
                            </select>
                            @error('kategori_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Nama Produk</label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $produk->nama) }}" required>
                            @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                            @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Harga</label>
                            <input type="number" name="harga" class="form-control @error('harga') is-invalid @enderror" value="{{ old('harga', $produk->harga) }}" required min="0">
                            @error('harga')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Stok</label>
                            <input type="number" name="stok" class="form-control @error('stok') is-invalid @enderror" value="{{ old('stok', $produk->stok) }}" required min="0">
                            @error('stok')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Gambar Produk</label><br>
                            @if($produk->gambar_url)
                                <img src="{{ $produk->gambar_url }}" alt="Gambar" width="80" class="mb-2"><br>
                            @endif
                            <input type="file" name="gambar" class="form-control-file @error('gambar') is-invalid @enderror">
                            @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Update</button>
                        <a href="{{ route('produk.index') }}" class="btn btn-light btn-block">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 