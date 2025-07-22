<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\KeranjangController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\AdminPaymentController;
use App\Http\Controllers\Api\ComplaintController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/auth/simple-reset-password', [App\Http\Controllers\Api\AuthController::class, 'simpleResetPassword']);

// Route yang memerlukan autentikasi
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/profile/photo', [AuthController::class, 'uploadProfilePhoto']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);
    
    // Cart routes (memerlukan login)
    Route::get('/cart', [KeranjangController::class, 'index']);
    Route::post('/cart', [KeranjangController::class, 'store']);
    Route::put('/cart/{id}', [KeranjangController::class, 'update']);
    Route::delete('/cart/{id}', [KeranjangController::class, 'destroy']);
    Route::delete('/cart', [KeranjangController::class, 'clear']);
    
    // Address routes (memerlukan login)
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::put('/addresses/{id}', [AddressController::class, 'update']);
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);
    
    // Order routes (memerlukan login)
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('orders/history-grouped', [\App\Http\Controllers\Api\OrderController::class, 'historyGrouped']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::get('/orders/{id}/history', [OrderController::class, 'orderHistory']);
    Route::get('/orders/by-number/{order_number}', [OrderController::class, 'showByOrderNumber']);
    Route::post('orders/{id}/confirm-received', [\App\Http\Controllers\Api\OrderController::class, 'confirmReceived']);
    Route::post('/orders/checkout-with-proof', [OrderController::class, 'checkoutWithProof']);
    
    // Payment routes (memerlukan login)
    Route::get('/payment/methods', [PaymentController::class, 'methods']);
    Route::get('/payment/bank-info', [PaymentController::class, 'bankInfo']);
    Route::post('/payment/upload-proof', [PaymentController::class, 'uploadProof']);
    Route::get('/payment/status/{orderId}', [PaymentController::class, 'status']);
    
    // Admin payment routes
    Route::get('/admin/payments/pending', [AdminPaymentController::class, 'pendingPayments']);
    Route::post('/admin/payments/{paymentId}/verify', [AdminPaymentController::class, 'verifyPayment']);
    Route::get('/admin/payments/statistics', [AdminPaymentController::class, 'statistics']);

    // Complaint routes (memerlukan login)
    Route::post('/orders/{orderId}/complaints', [ComplaintController::class, 'store']);
    Route::get('/orders/{orderId}/complaints', [ComplaintController::class, 'index']);
    Route::get('/complaints/{id}', [ComplaintController::class, 'show']);
    Route::put('/complaints/{id}', [ComplaintController::class, 'update']);
});

// Route untuk produk dan kategori (public)
Route::get('/products', [ProdukController::class, 'index']);
Route::get('/products/{id}', [ProdukController::class, 'show']);
Route::get('/products/category/{kategoriId}', [ProdukController::class, 'byCategory']);
Route::get('/categories', [KategoriController::class, 'index']);
Route::get('/categories/{id}', [KategoriController::class, 'show']);

// Shipping routes (public)
Route::get('/shipping/methods', [ShippingController::class, 'methods']);
Route::post('/shipping/cost', [ShippingController::class, 'cost']);

// Route untuk update shipping (admin/penjual only)
Route::middleware('auth:sanctum')->put('/shippings/{id}', [\App\Http\Controllers\Api\ShippingController::class, 'update']);
