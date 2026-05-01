<?php

use App\Http\Controllers\Admin\ArtController as AdminArtController;
use App\Http\Controllers\ArtController;
use App\Http\Controllers\ArtWorkController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckAccountController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\GuestCheckoutController;
use App\Http\Controllers\GuestLoginController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\VisitController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Home & About Routes
Route::get('/', function () {
    return view('ordinary.home.welcome.welcome');
})->name('home');

Route::get('/about', function () {
    return view('ordinary.about.about');
})->name('about');

// ========================================
// ART COLLECTION ROUTES (FIXED)
// ========================================
Route::prefix('art')->group(function () {
    Route::get('/collection', [ArtController::class, 'index'])->name('art.index');
    Route::get('/collection/{id}', [ArtController::class, 'show'])->name('art.show');
    Route::get('/collection/search', [ArtController::class, 'search'])->name('art.search');
});

// ========================================
// VISIT ROUTES
// ========================================
Route::prefix('plan-your-visit')->group(function () {
    Route::get('/', [VisitController::class, 'index'])->name('plan-your-visit.index');
    Route::get('/met-fifth-avenue', [VisitController::class, 'fifth'])->name('plan-your-visit.fifth');
    Route::get('/met-cloisters', [VisitController::class, 'cloisters'])->name('plan-your-visit.cloisters');
});

// ========================================
// AUTHENTICATION ROUTES
// ========================================
Route::prefix('account')->group(function () {
    // Guest routes (only for non-authenticated users)
    Route::middleware('guest')->group(function () {
        Route::get('/account-check', [CheckAccountController::class, 'show'])->name('account.account-check');
        Route::post('/account-check', [CheckAccountController::class, 'check'])->name('account.account-check.submit');
        Route::get('/register', [AuthController::class, 'register'])->name('account.register');
        Route::get('/login', [LoginController::class, 'show'])->name('account.login');
        Route::post('/login', [LoginController::class, 'login'])->name('account.login.submit');
        Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('account.forgot-password');
        Route::post('/forgot-password', [AuthController::class, 'handleForgotPassword'])->name('account.forgot-password.submit');
    });

    // Logout route (accessible to both authenticated and guest users)
    Route::post('/logout', [AuthController::class, 'logout'])->name('account.logout');

    // Protected routes (only for authenticated users)
    Route::middleware('auth')->group(function () {
        Route::get('/', [AuthController::class, 'account'])->name('account.index');
    });
});

Route::post('/register', [RegisterController::class, 'store'])
    ->middleware('guest')
    ->name('register.store');

// Canonical login route (required by Laravel auth middleware - redirects here when unauthenticated)
Route::get('/login', [LoginController::class, 'show'])->middleware('guest')->name('login');
Route::post('/guest-login', [GuestLoginController::class, 'store'])->name('guest.login');
Route::post('/guest-checkout', [GuestCheckoutController::class, 'store'])->name('guest.checkout');

// ========================================
// TICKET ROUTES
// ========================================
Route::prefix('tickets')->group(function () {
    Route::get('/', [TicketController::class, 'index'])->name('ticket.index');
    Route::get('/{schedule}', [TicketController::class, 'show'])->name('ticket.select');
    Route::post('/scan', [TicketController::class, 'scan'])->name('ticket.scan');
});

Route::get('/admission', [TicketController::class, 'index'])
    ->middleware('user.or.guest')
    ->name('ticket.admission');

Route::match(['get', 'post'], '/cart', [CartController::class, 'index'])->name('ticket.cart')->middleware('no.cache');
Route::delete('/cart/group/{id}', [CartController::class, 'removeGroup'])->name('cart.group.remove');
Route::get('/cart/group/{id}/modify', [CartController::class, 'modifyGroup'])->name('cart.group.modify');
Route::get('/cart/modify/cancel', [CartController::class, 'cancelModify'])->name('cart.modify.cancel');
Route::post('/cart/add', [CartController::class, 'add'])->name('ticket.add');
Route::post('/admission/cart/store', [CartController::class, 'storeAdmission'])->name('admission.cart.store');

Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('ticket.checkout.process');
Route::get('/checkout/payments/{order}', [CheckoutController::class, 'paymentPage'])->name('checkout.payments')->middleware('no.cache');
Route::post('/checkout/pay/{order}', [CheckoutController::class, 'pay'])->name('ticket.checkout.pay');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('ticket.checkout.success')->middleware('no.cache');

// ========================================
// MEMBERSHIP ROUTES (Protected - Requires Authentication)
// ========================================
Route::prefix('members')->middleware('auth')->group(function () {
    Route::get('/membership', [MembershipController::class, 'index'])->name('membership.index');
    Route::get('/membership/{id}', [MembershipController::class, 'show'])->name('membership.show');
    Route::post('/membership/purchase', [MembershipController::class, 'purchase'])->name('membership.purchase');
});

// ========================================
// LEGACY ROUTES (Keeping for compatibility)
// ========================================
Route::get('/art/{slug}', [ArtWorkController::class, 'show']);

Route::get('/order/create', [OrderController::class, 'create'])->name('order.create');
Route::post('/order/store', [OrderController::class, 'store'])->name('order.store');
Route::get('/order/show', [OrderController::class, 'index'])->name('order.show');
Route::get('/order/show/{order}', [OrderController::class, 'show'])->name('order.show.detail');

// ========================================
// ADMIN ROUTES (Protected - Admin Only)
// ========================================
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    // Admin Dashboard
    Route::get('/', [AdminArtController::class, 'dashboard'])->name('admin.dashboard');

    // Artwork Management - Art Collection Routes
    Route::get('/art', [AdminArtController::class, 'index'])->name('admin.art.index');
    Route::get('/art/create', [AdminArtController::class, 'create'])->name('admin.art.create');
    Route::post('/art', [AdminArtController::class, 'store'])->name('admin.art.store');
    Route::get('/art/{id}', [AdminArtController::class, 'show'])->name('admin.art.show');
    Route::get('/art/{id}/edit', [AdminArtController::class, 'edit'])->name('admin.art.edit');
    Route::put('/art/{id}', [AdminArtController::class, 'update'])->name('admin.art.update');
    Route::delete('/art/{id}', [AdminArtController::class, 'destroy'])->name('admin.art.destroy');
    Route::delete('/image/{imageId}', [AdminArtController::class, 'deleteImage'])->name('admin.art.image.delete');
});
Route::get('/force-logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    return 'logged out';
});
