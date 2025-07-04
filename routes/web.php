<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\GuestOfferController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\StatsController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Client\ProductController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\ClientOrderController;
use App\Http\Controllers\Client\ProductReviewController;
use App\Http\Middleware\EnsureTotpVerified;
use App\Http\Middleware\RoleMiddleware;

// === Landing & Home ===
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/home', fn () => redirect()->route('landing'))->name('home');

// === Guest (auth) ===
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    // Password reset (no email sending)
    Route::get('/forgot-password', [PasswordResetController::class, 'showRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetToken'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

// === Logout ===
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

// === 2FA (TOTP) ===
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/totp/setup', [TwoFactorController::class, 'showSetup'])->name('profile.totp.setup');
    Route::get('/2fa/verify', [TwoFactorController::class, 'showVerify'])->name('2fa.verify');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify']);
});

// === Post-login redirect
Route::get('/redirect', RedirectController::class)->middleware(['auth', EnsureTotpVerified::class])->name('redirect');

// === General Dashboard redirect
Route::get('/dashboard', fn () => redirect()->route('redirect'))->middleware(['auth', EnsureTotpVerified::class])->name('dashboard');

// === Profile
Route::middleware(['auth', EnsureTotpVerified::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// === Admin Panel
Route::prefix('admin')->middleware(['auth', EnsureTotpVerified::class, RoleMiddleware::class . ':1'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', AdminUserController::class)->except('show');
    Route::resource('products', AdminProductController::class)->except('show');

    Route::resource('orders', AdminOrderController::class);
    Route::post('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/complete', [AdminOrderController::class, 'complete'])->name('orders.complete');

    Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');
});

// === Staff Panel
Route::prefix('staff')->middleware(['auth', EnsureTotpVerified::class, RoleMiddleware::class . ':3'])->name('staff.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Staff\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [\App\Http\Controllers\Staff\StatsController::class, 'index'])->name('stats.index');

    Route::resource('products', \App\Http\Controllers\Staff\ProductController::class)->only(['index', 'edit', 'update']);
    Route::resource('orders', \App\Http\Controllers\Staff\OrderController::class);
    Route::post('/orders/{order}/cancel', [\App\Http\Controllers\Staff\OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/complete', [\App\Http\Controllers\Staff\OrderController::class, 'complete'])->name('orders.complete');

    Route::resource('users', \App\Http\Controllers\Staff\UserController::class)->except(['destroy', 'show']);

    Route::get('/products/{product}/promotion', [\App\Http\Controllers\Staff\ProductController::class, 'promotion'])->name('products.promotion');
    Route::post('/products/{product}/promotion', [\App\Http\Controllers\Staff\ProductController::class, 'storePromotion'])->name('products.promotion.store');
    Route::delete('/products/{product}/promotion', [\App\Http\Controllers\Staff\ProductController::class, 'removePromotion'])->name('products.promotion.remove');
});

// === Client Panel
Route::prefix('client')->middleware(['auth', EnsureTotpVerified::class, RoleMiddleware::class . ':2'])->name('client.')->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}/reviews', [ProductController::class, 'reviews'])->name('products.reviews');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/item/{item}', [CartController::class, 'remove'])->name('cart.remove');
    Route::patch('/cart', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/cart/checkout', [CartController::class, 'store'])->name('orders.store');
    Route::patch('/cart/update-dates', [CartController::class, 'updateDates'])->name('cart.updateDates');
    Route::post('/cart/extend', [CartController::class, 'extend'])->name('cart.extend');
    Route::post('/cart/repeat-order/{order}', [CartController::class, 'repeatOrder'])->name('cart.repeatOrder');

    Route::get('/orders', [ClientOrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{order}/cancel', [ClientOrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{order}/reviews/create', [ProductReviewController::class, 'create'])->name('orders.reviews.create');
    Route::post('/orders/{order}/reviews', [ProductReviewController::class, 'store'])->name('orders.reviews.store');
    Route::post('/orders/{order}/repeat', [ClientOrderController::class, 'repeat'])->name('orders.repeat');

    Route::get('/contact', fn () => view('client.contact'))->name('contact');
    Route::get('/profile', fn () => redirect()->route('profile.show'))->name('profile');
});

// === Public Offers
Route::get('/oferty', [GuestOfferController::class, 'index'])->name('guest.offers');

// === Local Dev Routes (TOTP debug etc.)
if (app()->environment('local')) {
    Route::post('/2fa/disable', function () {
        $user = auth()->user();
        $user->totp_secret = null;
        session()->forget('2fa_verified');
        $user->save();

        return redirect()->back()->with('status', 'Two-Factor Authentication has been disabled.');
    })->middleware(['auth'])->name('2fa.disable');

    Route::get('/dev/set-totp', function () {
        $user = auth()->user();
        $user->totp_secret = $user->totp_secret ?: 'JBSWY3DPEHPK3PXP';
        $user->save();

        return redirect()->route('2fa.verify')->with('status', 'Testowy kod TOTP przypisany.');
    })->middleware('auth')->name('dev.set-totp');

    Route::get('/test401', fn () => abort(401));
    Route::get('/test403', fn () => abort(403));
    Route::get('/test404', fn () => abort(404));
    Route::get('/test419', fn () => abort(419));
    Route::get('/test422', fn () => abort(422));
    Route::get('/test500', fn () => abort(500));
}
