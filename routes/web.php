<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\LaporanController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');

Route::resource('kategori', App\Http\Controllers\KategoriController::class)->middleware('auth');
Route::resource('produk', App\Http\Controllers\ProdukController::class)->middleware('auth');
Route::resource('pesanan', App\Http\Controllers\PesananController::class)->middleware('auth');
Route::resource('keranjang', App\Http\Controllers\KeranjangController::class)->middleware('auth');
Route::get('/laporan/produk', [LaporanController::class, 'produk'])->name('laporan.produk');
Route::get('/laporan/produk/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.produk.pdf');
Route::get('/laporan/penjualan', [LaporanController::class, 'penjualan'])->name('laporan.penjualan');
Route::get('/laporan/penjualan/pdf', [LaporanController::class, 'exportPenjualanPdf'])->name('laporan.penjualan.pdf');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/complaints', [ComplaintController::class, 'index'])->name('admin.complaints.index');
    Route::get('/admin/complaints/{id}', [ComplaintController::class, 'show'])->name('admin.complaints.show');
    Route::post('/admin/complaints/{id}/update', [ComplaintController::class, 'update'])->name('admin.complaints.update');
});