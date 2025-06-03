<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\Auth\PasswordResetController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;

// Strona główna
Route::get('/', fn () => view('welcome'))->name('home');

// Rejestracja i logowanie
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    // Reset hasła (bez maila)
    Route::get('/forgot-password', [PasswordResetController::class, 'showRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetToken'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

// Wylogowanie
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Przekierowanie po zalogowaniu
Route::get('/redirect', RedirectController::class)
    ->middleware('auth')
    ->name('redirect');

// Dashboard ogólny
Route::get('/dashboard', fn () => redirect()->route('redirect'))->name('dashboard');

// PANEL ADMINISTRATORA
Route::prefix('admin')->middleware(['auth', RoleMiddleware::class . ':1'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', fn () => 'Lista użytkowników')->name('users.index');

    Route::get('/orders/create', [AdminOrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [AdminOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}/edit', [AdminOrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:1'])->group(function () {
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);
    Route::patch('orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
});
    Route::put('/admin/orders/{order}', [AdminOrderController::class, 'update'])->name('admin.orders.update');

    Route::post('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');

    Route::get('/products', fn () => 'Lista produktów')->name('products.index');
    Route::get('/stats', fn () => 'Statystyki')->name('stats.index');
});


// PANEL KLIENTA
Route::prefix('client')->middleware(['auth', RoleMiddleware::class . ':2'])->name('client.')->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', fn () => 'Moje zamówienia')->name('orders.index');
    Route::get('/products', fn () => 'Produkty')->name('products.index');
});

// PANEL PRACOWNIKA
Route::prefix('staff')->middleware(['auth', RoleMiddleware::class . ':3'])->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', fn () => 'Zamówienia')->name('orders.index');
    Route::get('/products', fn () => 'Produkty')->name('products.index');
    Route::get('/stats', fn () => 'Statystyki')->name('stats.index');
});

// PROFIL UŻYTKOWNIKA
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// TESTOWE WIDOKI BŁĘDÓW (tylko w trybie local)
if (app()->environment('local')) {
    Route::get('/test401', fn() => abort(401));
    Route::get('/test403', fn() => abort(403));
    Route::get('/test404', fn() => abort(404));
    Route::get('/test419', fn() => abort(419));
    Route::get('/test422', fn() => abort(422));
    Route::get('/test500', fn() => abort(500));
}
