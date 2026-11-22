<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PackageManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\QueueController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - KOTHaK Photo Platform
|--------------------------------------------------------------------------
*/

// --- 1. Customer Public Facing Routes (Ambil Antrean, Tiket Digital, Riwayat) ---
Route::get('/', [BookingController::class, 'index'])->name('home');
Route::get('/packages', [BookingController::class, 'index'])->name('packages.index');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/history', [BookingController::class, 'history'])->name('booking.history');
Route::get('/ticket/{booking}', [BookingController::class, 'ticket'])->name('booking.ticket');
Route::get('/api/booking/{booking}/status', [BookingController::class, 'statusJson'])->name('api.booking.status');
Route::post('/ticket/{booking}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');

// --- 2. Live Queue Polling API (Digunakan layar kasir & customer) ---
Route::get('/api/queue/status', [QueueController::class, 'statusJson'])->name('api.queue.status');

// --- 3. Internal Authentication (Khusus Staf Kasir & Admin Studio) ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('auth.quick-login');

// --- 4. Cashier & Operator Multi-Booth Dashboard ---
Route::middleware(['auth', 'role:cashier,admin'])->prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/', [CashierController::class, 'dashboard'])->name('dashboard');
    Route::post('/verify/{booking}', [CashierController::class, 'verifyPayment'])->name('verify');
    Route::post('/checkin/{booking}', [CashierController::class, 'checkIn'])->name('checkin');
    Route::post('/call-next', [CashierController::class, 'callNext'])->name('call-next');
    Route::post('/call/{booking}', [CashierController::class, 'callSpecific'])->name('call-specific');
    Route::post('/complete/{booking}', [CashierController::class, 'completeSession'])->name('complete');
    Route::post('/no-show/{booking}', [CashierController::class, 'markNoShow'])->name('no-show');
    Route::post('/walk-in', [CashierController::class, 'storeWalkIn'])->name('walkin.store');
});

// --- 5. Admin Studio Management & Reporting (Admin & Kasir) ---
Route::middleware(['auth', 'role:admin,cashier'])->prefix('admin')->name('admin.')->group(function () {
    // Analytics & Reporting
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports/csv', [AdminDashboardController::class, 'exportCsv'])->name('reports.csv');
    Route::get('/reports/print', [AdminDashboardController::class, 'printReport'])->name('reports.print');

    // User Accounts Management (Kasir & Admin)
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/toggle', [UserManagementController::class, 'toggleActive'])->name('users.toggle');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    // Photo Packages (3 Studio Booths)
    Route::get('/packages', [PackageManagementController::class, 'index'])->name('packages.index');
    Route::post('/packages', [PackageManagementController::class, 'store'])->name('packages.store');
    Route::put('/packages/{package}', [PackageManagementController::class, 'update'])->name('packages.update');
    Route::post('/packages/{package}/toggle', [PackageManagementController::class, 'toggleActive'])->name('packages.toggle');
    Route::delete('/packages/{package}', [PackageManagementController::class, 'destroy'])->name('packages.destroy');
});
