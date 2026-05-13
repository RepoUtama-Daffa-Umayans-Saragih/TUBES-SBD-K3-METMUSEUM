<?php

// =========================
// IMPORTS (ALL AT TOP)
// =========================
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\ArtworkController as AdminArtworkController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExhibitionController as AdminExhibitionController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\TicketAnalyticsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
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

// =========================
// HOME ROUTES
// =========================
Route::get('/', function () {
    return view('ordinary.home.welcome.welcome');
})->name('home');
Route::get('/about', function () {
    return view('ordinary.about.about');
})->name('about');


// =========================
// ART COLLECTION ROUTES
// =========================
Route::prefix('art')->group(function () {
    Route::get('/collection', [ArtController::class, 'index'])->name('art.index');
    Route::get('/curatorial-areas', [ArtController::class, 'curatorialAreas'])->name('art.curatorial-areas');
    Route::get('/collection/search', [ArtController::class, 'search'])->name('art.search');
    Route::get('/collection/{id}', [ArtController::class, 'show'])->name('art.show');
});

// =========================
// VISIT ROUTES
// =========================
Route::prefix('plan-your-visit')->group(function () {
    Route::get('/', [VisitController::class, 'index'])->name('plan-your-visit.index');
    Route::get('/met-fifth-avenue', [VisitController::class, 'fifth'])->name('plan-your-visit.fifth');
    Route::get('/met-cloisters', [VisitController::class, 'cloisters'])->name('plan-your-visit.cloisters');
});

// =========================
// AUTHENTICATION ROUTES
// =========================
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

// =========================
// TICKET ROUTES
// =========================
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
// Fallback for missing checkout GET route to prevent RouteNotFoundException
Route::get('/checkout', function () {
    $cartItems = collect();
    $customer  = ['name' => '', 'email' => ''];
    return view('ordinary.checkout.form', compact('cartItems', 'customer'));
})->name('ticket.checkout');
Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('ticket.checkout.process');
Route::get('/checkout/payments/{order}', [CheckoutController::class, 'paymentPage'])->name('checkout.payments')->middleware('no.cache');
Route::post('/checkout/pay/{order}', [CheckoutController::class, 'pay'])->name('ticket.checkout.pay');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('ticket.checkout.success')->middleware('no.cache');

// =========================
// MEMBERSHIP ROUTES
// =========================
Route::prefix('members')->group(function () {
    Route::get('/membership', [MembershipController::class, 'index'])->name('membership.index');
    Route::get('/membership/{id}', [MembershipController::class, 'show'])->name('membership.show');
});
// Purchase requires authentication
Route::post('/members/membership/purchase', [MembershipController::class, 'purchase'])
    ->middleware('auth')
    ->name('membership.purchase');

// =========================
// LEGACY COMPATIBILITY ROUTES
// =========================
Route::get('/art/{slug}', [ArtWorkController::class, 'show'])->name('artwork.show');
Route::get('/order/create', [OrderController::class, 'create'])->name('order.create');
Route::post('/order/store', [OrderController::class, 'store'])->name('order.store');
Route::get('/order/show', [OrderController::class, 'index'])->name('order.show');
Route::get('/order/show/{order}', [OrderController::class, 'show'])->name('order.show.detail');

// =========================
// ADMIN ROUTES (Modern Admin Skeleton)
// =========================
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/artworks', [AdminArtworkController::class, 'index'])->name('artworks.index');
    Route::get('/exhibitions', [AdminExhibitionController::class, 'index'])->name('exhibitions.index');
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    
    // Ticket Analytics Dashboard
    Route::prefix('ticket-analytics')->name('ticket-analytics.')->group(function () {
        Route::get('/', [TicketAnalyticsController::class, 'index'])->name('index');
        Route::get('/data', [TicketAnalyticsController::class, 'getAnalyticsData'])->name('data');
    });
    
    // Payment Management Dashboard
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/', [AdminPaymentController::class, 'index'])->name('index');
        Route::get('/data', [AdminPaymentController::class, 'getData'])->name('data');
    });
});
Route::get('/force-logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return 'logged out';
});
Route::get('/visit-guides/accessibility', function () {
    return view('ordinary.plan-your-visit.accessibility.accessibility');
})->name('visit.accessibility');
Route::get('/member/membership', function () {
    return view('ordinary.member.membership.membership');
})->name('member.membership');
Route::get('/plan-your-visit/fifth/learn-more', function () {
    return view('ordinary.plan-your-visit.fifth.learn-more');
})->name('learn.more');
Route::get('/plan-your-visit/cloister/learn-more', function () {
    return view('ordinary.plan-your-visit.cloister.learn-more');
})->name('cloister.learn.more');
Route::get('/plan-your-visit/accessibility/cloisters', function () {
    return view('ordinary.plan-your-visit.accessibility.accessibility-cloisters');
})->name('accessibility.cloisters');
Route::get('/plan-your-visit/accessibility', function () {
    return view('ordinary.plan-your-visit.accessibility.accessibility');
})->name('accessibility.main');
Route::get('/admin-preview', function () {
    return view('admin.dashboard.index');
});
