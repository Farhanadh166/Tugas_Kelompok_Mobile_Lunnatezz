@extends('layouts.app')
@section('title', 'Edit Kategori')
@section('content')
<div class="section-header">
    <h1>Edit Kategori</h1>
</div>
<div class="section-body">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('kategori.update', $kategori) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Nama Kategori</label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $kategori->nama) }}" required>
                            @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
                            @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Update</button>
                        <a href="{{ route('kategori.index') }}" class="btn btn-light btn-block">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 