<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PackageManagementController;
use App\Http\Controllers\Admin\ScheduleManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QueueController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - KOTHaK Photo Platform
|--------------------------------------------------------------------------
*/

// --- 1. Customer & Public Facing Routes ---
Route::get('/', [BookingController::class, 'index'])->name('home');
Route::get('/packages', [BookingController::class, 'index'])->name('packages.index');
Route::get('/schedule/{package}', [BookingController::class, 'selectSchedule'])->name('schedule.select');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/history', [BookingController::class, 'history'])->name('booking.history');
Route::get('/ticket/{booking}', [BookingController::class, 'ticket'])->name('booking.ticket');

// --- 2. QRIS Payment & Checkout ---
Route::get('/checkout/{booking}', [PaymentController::class, 'checkout'])->name('payment.checkout');
Route::post('/checkout/{booking}/payment', [PaymentController::class, 'uploadProof'])->name('payment.upload');

// --- 3. Public Smart Queue TV Display & Live API ---
Route::get('/display', [QueueController::class, 'display'])->name('queue.display');
Route::get('/queue/display', [QueueController::class, 'display']);
Route::get('/api/queue/status', [QueueController::class, 'statusJson'])->name('api.queue.status');

// --- 4. Authentication (FR-01) ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('auth.quick-login');

// --- 5. Cashier & Operator Dashboard (FR-08) ---
Route::middleware(['auth', 'role:cashier,admin'])->prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/', [CashierController::class, 'dashboard'])->name('dashboard');
    Route::post('/verify/{booking}', [CashierController::class, 'verifyPayment'])->name('verify');
    Route::post('/checkin/{booking}', [CashierController::class, 'checkIn'])->name('checkin');
    Route::post('/call-next', [CashierController::class, 'callNext'])->name('call-next');
    Route::post('/call/{booking}', [CashierController::class, 'callSpecific'])->name('call-specific');
    Route::post('/complete/{booking}', [CashierController::class, 'completeSession'])->name('complete');
    Route::post('/walk-in', [CashierController::class, 'storeWalkIn'])->name('walkin.store');
});

// --- 6. Admin Studio Management & Reporting (FR-01, FR-02, FR-07, FR-09) ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Analytics & Reporting (FR-09)
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports/csv', [AdminDashboardController::class, 'exportCsv'])->name('reports.csv');
    Route::get('/reports/print', [AdminDashboardController::class, 'printReport'])->name('reports.print');

    // User Accounts Management (FR-01)
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/toggle', [UserManagementController::class, 'toggleActive'])->name('users.toggle');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    // Photo Packages & Add-ons (FR-07)
    Route::get('/packages', [PackageManagementController::class, 'index'])->name('packages.index');
    Route::post('/packages', [PackageManagementController::class, 'store'])->name('packages.store');
    Route::put('/packages/{package}', [PackageManagementController::class, 'update'])->name('packages.update');
    Route::post('/packages/{package}/toggle', [PackageManagementController::class, 'toggleActive'])->name('packages.toggle');
    Route::delete('/packages/{package}', [PackageManagementController::class, 'destroy'])->name('packages.destroy');
    Route::post('/add-ons', [PackageManagementController::class, 'storeAddOn'])->name('addons.store');
    Route::put('/add-ons/{addOn}', [PackageManagementController::class, 'updateAddOn'])->name('addons.update');
    Route::delete('/add-ons/{addOn}', [PackageManagementController::class, 'destroyAddOn'])->name('addons.destroy');

    // Daily Schedule & Slots (FR-02)
    Route::get('/schedules', [ScheduleManagementController::class, 'index'])->name('schedules.index');
    Route::post('/schedules', [ScheduleManagementController::class, 'store'])->name('schedules.store');
    Route::post('/schedules/batch', [ScheduleManagementController::class, 'generateBatch'])->name('schedules.batch');
    Route::delete('/schedules/{schedule}', [ScheduleManagementController::class, 'destroy'])->name('schedules.destroy');
});
