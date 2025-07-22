<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;

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

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/complaints', [ComplaintController::class, 'index'])->name('admin.complaints.index');
    Route::get('/admin/complaints/{id}', [ComplaintController::class, 'show'])->name('admin.complaints.show');
    Route::post('/admin/complaints/{id}/update', [ComplaintController::class, 'update'])->name('admin.complaints.update');
});
