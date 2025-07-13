<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

// Route untuk akses gambar bukti pembayaran
Route::get('/payment-proof/{filename}', function ($filename) {
    $path = storage_path('app/public/payment_proofs/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path);
})->where('filename', '.*');

Route::resource('kategori', App\Http\Controllers\KategoriController::class)->middleware('auth');
Route::resource('produk', App\Http\Controllers\ProdukController::class)->middleware('auth');
Route::resource('pesanan', App\Http\Controllers\PesananController::class)->middleware('auth');
Route::resource('keranjang', App\Http\Controllers\KeranjangController::class)->middleware('auth');
Route::resource('pembayaran', App\Http\Controllers\PembayaranController::class)->middleware('auth');
Route::post('pembayaran/{pembayaran}/update-status', [App\Http\Controllers\PembayaranController::class, 'updateStatus'])->name('pembayaran.update-status')->middleware('auth');
Route::get('pembayaran-filter', [App\Http\Controllers\PembayaranController::class, 'filter'])->name('pembayaran.filter')->middleware('auth');
