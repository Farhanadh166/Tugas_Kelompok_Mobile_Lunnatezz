@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="section-header" style="background: linear-gradient(90deg, #a78bfa 0%, #f3e8ff 100%); border-radius: 12px; margin-bottom: 32px; box-shadow: 0 2px 12px rgba(160,120,200,0.08);">
    <h1 style="color:#f3e8ff; font-weight:700; letter-spacing:1px;">Dashboard Admin</h1>
</div>
<div class="section-body">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card" style="border-radius:18px; box-shadow:0 4px 24px rgba(160,120,200,0.10);">
                <div class="card-body text-center" style="padding:48px 24px;">
                    <h2 style="color:#7c3aed; font-size:2.2rem; font-weight:700; margin-bottom:12px;">Selamat datang, {{ Auth::user()->nama }}!</h2>
                    <p style="font-size:1.15rem; color:#6d28d9; margin-bottom:24px;">Anda berhasil login sebagai admin <b>Lunneettez</b>.</p>
                    <div style="font-size:1.05rem; color:#444; margin-bottom:8px;">Aplikasi <b>Lunneettez</b> adalah platform penjualan aksesoris online dengan tampilan modern, elegan, dan mudah digunakan.<br>Kelola kategori, produk, dan transaksi dengan mudah melalui dashboard ini.</div>
                    <div style="margin-top:32px;">
                        <span style="background:#f3e8ff; color:#7c3aed; padding:8px 24px; border-radius:20px; font-weight:600; font-size:1rem;">"Perancangan Aplikasi Penjualan Aksesoris Lunneettez Online Berbasis Flutter & Laravel API"</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 