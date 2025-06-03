<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\EnsureTotpVerified;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Admin\StatsController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\UserController;

// Home Page
Route::get('/', fn () => view('welcome'))->name('home');

// Registration and Login
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    // Password reset (without email)
    Route::get('/forgot-password', [PasswordResetController::class, 'showRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetToken'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// TOTP routes (setup + verification)
Route::middleware(['auth'])->group(function () {
    Route::get('/2fa/setup', [TwoFactorController::class, 'showSetup'])->name('2fa.setup');
    Route::get('/2fa/verify', [TwoFactorController::class, 'showVerify'])->name('2fa.verify');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify']);
});

// Redirect after login
Route::get('/redirect', RedirectController::class)
    ->middleware(['auth', EnsureTotpVerified::class])
    ->name('redirect');

// General dashboard
Route::get('/dashboard', fn () => redirect()->route('redirect'))
    ->middleware(['auth', EnsureTotpVerified::class])
    ->name('dashboard');

// ADMIN PANEL
Route::prefix('admin')
    ->middleware(['auth', EnsureTotpVerified::class, RoleMiddleware::class . ':1'])
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', fn () => 'User list')->name('users.index');

        // Full CRUD for Orders

    Route::get('/orders/create', [AdminOrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [AdminOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}/edit', [AdminOrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:1'])->group(function () {
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);
});
    Route::post('/admin/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('admin.orders.cancel');

    Route::put('/admin/orders/{order}', [AdminOrderController::class, 'update'])->name('admin.orders.update');

    Route::post('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');

    Route::get('/products', fn () => 'Product list')->name('products.index');
    Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');
    });

   // STATS
    Route::prefix('admin')
        ->middleware(['auth', EnsureTotpVerified::class, RoleMiddleware::class . ':1'])
        ->name('admin.')
        ->group(function () {
            Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');
    });


// CLIENT PANEL
Route::prefix('client')
    ->middleware(['auth', EnsureTotpVerified::class, RoleMiddleware::class . ':2'])
    ->name('client.')
    ->group(function () {
        Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', fn () => 'Moje zamówienia')->name('orders.index');
        Route::get('/products', fn () => 'Produkty')->name('products.index');
    });



// STAFF PANEL
    Route::prefix('staff')
        ->middleware(['auth', EnsureTotpVerified::class, RoleMiddleware::class . ':3'])
        ->name('staff.')
        ->group(function () {
            Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
            Route::get('/orders', fn () => 'Zamówienia')->name('orders.index');
            Route::get('/products', fn () => 'Produkty')->name('products.index');
            Route::get('/stats', [App\Http\Controllers\Staff\StatsController::class, 'index'])->name('stats.index');
        });



// USER PROFILE
Route::middleware(['auth', EnsureTotpVerified::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// TESTOWE WIDOKI BŁĘDÓW (tylko w trybie local)
if (app()->environment('local')) {
    // Disable TOTP manually
    Route::post('/2fa/disable', function () {
        $user = auth()->user();
        $user->totp_secret = null;
        session()->forget('2fa_verified');
        $user->save();

        return redirect()->back()->with('status', 'Two-Factor Authentication has been disabled.');
    })->middleware(['auth'])->name('2fa.disable');

    // Set test TOTP for logged-in user
    Route::get('/dev/set-totp', function () {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Brak zalogowanego użytkownika.');
        }

        if (!$user->totp_secret) {
            $user->totp_secret = 'JBSWY3DPEHPK3PXP';
            $user->save();
        }

        return redirect()->route('2fa.verify')->with('status', 'Testowy kod TOTP przypisany.');
    })->middleware('auth')->name('dev.set-totp');

    // Test HTTP error views
    Route::get('/test401', fn () => abort(401));
    Route::get('/test403', fn () => abort(403));
    Route::get('/test404', fn () => abort(404));
    Route::get('/test419', fn () => abort(419));
    Route::get('/test422', fn () => abort(422));
    Route::get('/test500', fn () => abort(500));
}
