<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Halaman Utama & Prototype All-in-One Identik dengan Awal (kothak-photo-main.zip)
Route::get('/', [BookingController::class, 'index'])->name('home');
Route::get('/packages', [BookingController::class, 'index'])->name('packages.index');
Route::get('/index.html', function () {
    return response(file_get_contents(public_path('index.html')));
});
Route::get('/blueprint', function () {
    return response(file_get_contents(public_path('index.html')));
})->name('blueprint');

// Pemilihan Jadwal & Booking
Route::get('/schedule/{package}', [BookingController::class, 'selectSchedule'])->name('schedule.select');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/history', [BookingController::class, 'history'])->name('booking.history');

// Pembayaran & Checkout QRIS
Route::get('/checkout/{booking}', [PaymentController::class, 'checkout'])->name('payment.checkout');
Route::post('/checkout/{booking}/payment', [PaymentController::class, 'uploadProof'])->name('payment.upload');

// Modul Kasir
Route::prefix('cashier')->group(function () {
    Route::get('/', [CashierController::class, 'dashboard'])->name('cashier.dashboard');
    Route::post('/verify/{booking}', [CashierController::class, 'verifyPayment'])->name('cashier.verify');
    Route::post('/checkin/{booking}', [CashierController::class, 'checkIn'])->name('cashier.checkin');
    Route::post('/call-next', [CashierController::class, 'callNext'])->name('cashier.call-next');
});

Route::get('/admin', [CashierController::class, 'dashboard'])->name('admin.dashboard');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('auth.quick-login');


