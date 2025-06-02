<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\RoleMiddleware;

// Strona główna
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rejestracja i logowanie
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// Wylogowanie
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Przekierowanie po zalogowaniu wg roli
Route::get('/redirect', RedirectController::class)
    ->middleware('auth')
    ->name('redirect');

// Admin
Route::middleware(['auth', RoleMiddleware::class.':1'])->group(function () {
    Route::get('/admin', fn() => view('admin.dashboard'))->name('admin.dashboard');
});

// Client
Route::middleware(['auth', RoleMiddleware::class.':2'])->group(function () {
    Route::get('/client', fn() => view('client.dashboard'))->name('client.dashboard');
});

// Staff
Route::middleware(['auth', RoleMiddleware::class.':3'])->group(function () {
    Route::get('/staff', fn() => view('staff.dashboard'))->name('staff.dashboard');
});

// Profil użytkownika
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/dashboard', fn() => redirect()->route('redirect'))->name('dashboard');

// Route::get('/test401', fn() => abort(401));
// Route::get('/test403', fn() => abort(403));
// Route::get('/test404', fn() => abort(404));
// Route::get('/test419', fn() => abort(419));
// Route::get('/test422', fn() => abort(422));
// Route::get('/test500', fn() => abort(500));