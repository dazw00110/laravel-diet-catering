<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PurchaseHistoryController;
use App\Http\Controllers\CateringCalendarController;
use App\Http\Controllers\StatsController;

// Widok powitalny
Route::get('/', function () {
    return view('welcome');
});

// Przekierowanie po zalogowaniu
Route::get('/redirect', RedirectController::class)->middleware(['auth']);

// Dashboardy
Route::middleware(['auth'])->group(function () {
    Route::view('/admin', 'admin.dashboard')->middleware('role:admin');
    Route::view('/client', 'client.dashboard')->middleware('role:client');
    Route::view('/staff', 'staff.dashboard')->middleware('role:staff');
});

// Routing dla klienta
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

    Route::get('/history', [PurchaseHistoryController::class, 'index'])->name('history.index');
    Route::get('/calendar', [CateringCalendarController::class, 'index'])->name('calendar.index');
});

// Routing dla pracownika
Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/orders/manage', [OrderController::class, 'manage'])->name('orders.manage');
    Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');
});

// Auth (dodawane przez Breeze)
require __DIR__.'/auth.php';
