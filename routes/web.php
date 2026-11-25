<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FootballMatchController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminMatchController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminStadiumController;
use App\Http\Controllers\Admin\AdminTicketCategoryController;
use App\Http\Controllers\Admin\AdminSanctionController;

/*
|--------------------------------------------------------------------------
| Halaman Publik
|--------------------------------------------------------------------------
*/

// Pertandingan Sepak Bola
Route::get('/', [FootballMatchController::class, 'index'])->name('home');
Route::get('/matches', [FootballMatchController::class, 'index'])->name('matches.index');
Route::get('/matches/{id}', [FootballMatchController::class, 'show'])->name('matches.show');

// Webhook / Callback Notification dari Payment Gateway (Midtrans / Xendit)
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

/*
|--------------------------------------------------------------------------
| Halaman User (Membutuhkan Autentikasi Pengguna: auth)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboard User
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // 1. Kelola & Riwayat Pesanan (OrderController)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/matches/{match}/order', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/matches/{match}/order', [OrderController::class, 'store'])->name('orders.store');

    // 2. Pembayaran (PaymentController)
    Route::get('/payment/{order}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{order}/confirm', [PaymentController::class, 'confirm'])->name('payment.confirm');

    // 3. E-Ticket (TicketController)
    Route::get('/my-tickets', [TicketController::class, 'myTickets'])->name('tickets.my');
    Route::get('/tickets/{id}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{id}/download', [TicketController::class, 'download'])->name('tickets.download');
    Route::get('/tickets/{ticket}/pdf', [TicketController::class, 'downloadPdf'])->name('tickets.pdf');
});

/*
|--------------------------------------------------------------------------
| Panel Admin (harus login + role admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard admin
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // CRUD Pertandingan
    Route::resource('matches', AdminMatchController::class);

    // CRUD Stadion
    Route::resource('stadiums', AdminStadiumController::class);

    // CRUD Kategori Tiket
    Route::resource('ticket-categories', AdminTicketCategoryController::class);

    // Kelola Pesanan
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // 5. Fitur Kelima: Kelola Sanksi & Blacklist Suporter
    Route::get('sanctions', [AdminSanctionController::class, 'index'])->name('sanctions.index');
    Route::get('sanctions/{user}', [AdminSanctionController::class, 'show'])->name('sanctions.show');
    Route::post('sanctions/{user}/ban', [AdminSanctionController::class, 'ban'])->name('sanctions.ban');
    Route::post('sanctions/{user}/revoke', [AdminSanctionController::class, 'revoke'])->name('sanctions.revoke');
});

require __DIR__.'/auth.php';
