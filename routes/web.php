<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedirectController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', RedirectController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'role:2'])->group(function () {
    Route::get('/client', function () {
        return view('client.dashboard');
    })->name('client.dashboard');
});

Route::middleware(['auth', 'role:3'])->group(function () {
    Route::get('/staff', function () {
        return view('staff.dashboard');
    })->name('staff.dashboard');
});

Route::middleware(['auth', 'role:1'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
