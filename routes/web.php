<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Dashboard\ContactController as DashboardContactController;
use App\Http\Controllers\Dashboard\SettingsController;

// Home Page
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Contact Routes
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Policy Pages
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/terms', 'terms')->name('terms');
Route::view('/refund-policy', 'refund-policy')->name('refund');

// Products Routes (Public)
Route::get('/products', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show');

// AI Chat API Routes
Route::prefix('api')->name('api.')->group(function () {
    Route::post('/ai-chat', [App\Http\Controllers\Api\AIChatController::class, 'chat'])->name('ai-chat');
    Route::get('/product-suggestions', [App\Http\Controllers\Api\AIChatController::class, 'getProductSuggestions'])->name('product-suggestions');
});

// Cart Routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [App\Http\Controllers\CartController::class, 'index'])->name('index');
    Route::post('/add', [App\Http\Controllers\CartController::class, 'add'])->name('add');
    Route::put('/{cartItem}/update', [App\Http\Controllers\CartController::class, 'update'])->name('update');
    Route::delete('/{cartItem}/remove', [App\Http\Controllers\CartController::class, 'remove'])->name('remove');
    Route::post('/apply-coupon', [App\Http\Controllers\CartController::class, 'applyCoupon'])->name('apply-coupon');
    Route::post('/remove-coupon', [App\Http\Controllers\CartController::class, 'removeCoupon'])->name('remove-coupon');
});

// Checkout Routes
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [App\Http\Controllers\CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [App\Http\Controllers\CheckoutController::class, 'process'])->name('process');
    Route::get('/success/{order}', [App\Http\Controllers\CheckoutController::class, 'success'])->name('success');
});

// Profile Routes (Requires Auth)
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [App\Http\Controllers\ProfileController::class, 'index'])->name('index');
    Route::get('/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('update');
    Route::get('/orders', [App\Http\Controllers\ProfileController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [App\Http\Controllers\ProfileController::class, 'orderDetails'])->name('order-details');
    Route::get('/loyalty-points', [App\Http\Controllers\ProfileController::class, 'loyaltyPoints'])->name('loyalty-points');
    Route::get('/referrals', [App\Http\Controllers\ProfileController::class, 'referrals'])->name('referrals');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [AuthController::class, 'showPasswordResetForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showPasswordReset'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    // Verification Routes
    Route::get('/verification', [AuthController::class, 'showVerificationForm'])->name('verification.show');
    Route::post('/verification', [AuthController::class, 'verify'])->name('verification.verify');
    Route::post('/verification/resend', [AuthController::class, 'resendVerificationCode'])->name('verification.resend');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.change.update');
});

// Dashboard Routes - Only for admin, manager, and employee
Route::middleware(['auth', 'track.session', 'role:admin,manager,employee'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Reports Routes
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
    Route::get('/reports/export', [DashboardController::class, 'exportSalesReport'])->name('reports.export');

    // Products Routes
    Route::get('/products', function () {
        return view('dashboard.products');
    })->name('products');

    // Email Management Routes
    Route::prefix('email')->name('email.')->group(function () {
        Route::get('/', [App\Http\Controllers\Dashboard\EmailController::class, 'index'])->name('index');
        Route::post('/send', [App\Http\Controllers\Dashboard\EmailController::class, 'sendTestEmail'])->name('send');
        Route::post('/bulk-send', [App\Http\Controllers\Dashboard\EmailController::class, 'sendBulkEmails'])->name('bulk-send');
        Route::get('/status', [App\Http\Controllers\Dashboard\EmailController::class, 'getEmailStatus'])->name('status');
        Route::post('/test-config', [App\Http\Controllers\Dashboard\EmailController::class, 'testEmailConfig'])->name('test-config');
    });

    // Orders Management Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [App\Http\Controllers\Dashboard\OrderController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Dashboard\OrderController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Dashboard\OrderController::class, 'store'])->name('store');
        Route::get('/{order}', [App\Http\Controllers\Dashboard\OrderController::class, 'show'])->name('show');
        Route::get('/{order}/edit', [App\Http\Controllers\Dashboard\OrderController::class, 'edit'])->name('edit');
        Route::put('/{order}', [App\Http\Controllers\Dashboard\OrderController::class, 'update'])->name('update');
        Route::delete('/{order}', [App\Http\Controllers\Dashboard\OrderController::class, 'destroy'])->name('destroy');
        Route::patch('/{order}/update-status', [App\Http\Controllers\Dashboard\OrderController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{order}/update-payment-status', [App\Http\Controllers\Dashboard\OrderController::class, 'updatePaymentStatus'])->name('update-payment-status');
        Route::patch('/{orderItem}/update-item-status', [App\Http\Controllers\Dashboard\OrderController::class, 'updateItemStatus'])->name('update-item-status');
        Route::get('/export/csv', [App\Http\Controllers\Dashboard\OrderController::class, 'export'])->name('export');
        Route::get('/statistics', [App\Http\Controllers\Dashboard\OrderController::class, 'statistics'])->name('statistics');
    });

    // Customers Routes
    Route::get('/customers', function () {
        return view('dashboard.customers');
    })->name('customers');

    // Coupons Management Routes
    Route::prefix('coupons')->name('coupons.')->group(function () {
        Route::get('/', [App\Http\Controllers\Dashboard\CouponController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Dashboard\CouponController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Dashboard\CouponController::class, 'store'])->name('store');
        Route::get('/{coupon}', [App\Http\Controllers\Dashboard\CouponController::class, 'show'])->name('show');
        Route::get('/{coupon}/edit', [App\Http\Controllers\Dashboard\CouponController::class, 'edit'])->name('edit');
        Route::put('/{coupon}', [App\Http\Controllers\Dashboard\CouponController::class, 'update'])->name('update');
        Route::delete('/{coupon}', [App\Http\Controllers\Dashboard\CouponController::class, 'destroy'])->name('destroy');
        Route::patch('/{coupon}/toggle-status', [App\Http\Controllers\Dashboard\CouponController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{coupon}/duplicate', [App\Http\Controllers\Dashboard\CouponController::class, 'duplicate'])->name('duplicate');
        Route::get('/export/csv', [App\Http\Controllers\Dashboard\CouponController::class, 'export'])->name('export');
        Route::get('/usage/stats', [App\Http\Controllers\Dashboard\CouponController::class, 'usageStats'])->name('usage-stats');
        Route::post('/validate', [App\Http\Controllers\Dashboard\CouponController::class, 'validateCoupon'])->name('validate');
    });

    // Notifications Management Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Dashboard\NotificationController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Dashboard\NotificationController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Dashboard\NotificationController::class, 'store'])->name('store');
        Route::get('/{notification}', [App\Http\Controllers\Dashboard\NotificationController::class, 'show'])->name('show');
        Route::get('/{notification}/edit', [App\Http\Controllers\Dashboard\NotificationController::class, 'edit'])->name('edit');
        Route::put('/{notification}', [App\Http\Controllers\Dashboard\NotificationController::class, 'update'])->name('update');
        Route::delete('/{notification}', [App\Http\Controllers\Dashboard\NotificationController::class, 'destroy'])->name('destroy');
        Route::post('/{notification}/mark-read', [App\Http\Controllers\Dashboard\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/{notification}/mark-unread', [App\Http\Controllers\Dashboard\NotificationController::class, 'markAsUnread'])->name('mark-unread');
        Route::post('/{notification}/mark-sent', [App\Http\Controllers\Dashboard\NotificationController::class, 'markAsSent'])->name('mark-sent');
        Route::post('/{notification}/mark-failed', [App\Http\Controllers\Dashboard\NotificationController::class, 'markAsFailed'])->name('mark-failed');
        Route::post('/{notification}/retry', [App\Http\Controllers\Dashboard\NotificationController::class, 'retry'])->name('retry');
        Route::post('/{notification}/send-now', [App\Http\Controllers\Dashboard\NotificationController::class, 'sendNow'])->name('send-now');
        Route::post('/bulk-mark-read', [App\Http\Controllers\Dashboard\NotificationController::class, 'bulkMarkAsRead'])->name('bulk-mark-read');
        Route::post('/bulk-delete', [App\Http\Controllers\Dashboard\NotificationController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('/export/csv', [App\Http\Controllers\Dashboard\NotificationController::class, 'export'])->name('export');
        Route::get('/stats', [App\Http\Controllers\Dashboard\NotificationController::class, 'stats'])->name('stats');
    });

    // Contacts Management Routes
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [DashboardContactController::class, 'index'])->name('index');
        Route::get('/{contact}', [DashboardContactController::class, 'show'])->name('show');
        Route::get('/{contact}/edit', [DashboardContactController::class, 'edit'])->name('edit');
        Route::put('/{contact}', [DashboardContactController::class, 'update'])->name('update');
        Route::delete('/{contact}', [DashboardContactController::class, 'destroy'])->name('destroy');
        Route::post('/{contact}/mark-in-progress', [DashboardContactController::class, 'markAsInProgress'])->name('mark-in-progress');
        Route::post('/{contact}/mark-resolved', [DashboardContactController::class, 'markAsResolved'])->name('mark-resolved');
        Route::post('/{contact}/mark-closed', [DashboardContactController::class, 'markAsClosed'])->name('mark-closed');
        Route::post('/{contact}/mark-spam', [DashboardContactController::class, 'markAsSpam'])->name('mark-spam');
        Route::post('/{contact}/assign', [DashboardContactController::class, 'assignTo'])->name('assign');
        Route::post('/{contact}/respond', [DashboardContactController::class, 'respond'])->name('respond');
        Route::post('/{contact}/update-priority', [DashboardContactController::class, 'updatePriority'])->name('update-priority');
        Route::post('/{contact}/update-type', [DashboardContactController::class, 'updateType'])->name('update-type');
        Route::post('/bulk-action', [DashboardContactController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/export/csv', [DashboardContactController::class, 'export'])->name('export');
        Route::get('/stats', [DashboardContactController::class, 'stats'])->name('stats');
    });

    // Settings Management Routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/', [SettingsController::class, 'update'])->name('update');
        Route::get('/create', [SettingsController::class, 'create'])->name('create');
        Route::post('/', [SettingsController::class, 'store'])->name('store');
        Route::get('/{setting}/edit', [SettingsController::class, 'edit'])->name('edit');
        Route::put('/{setting}', [SettingsController::class, 'updateSetting'])->name('update-setting');
        Route::delete('/{setting}', [SettingsController::class, 'destroy'])->name('destroy');
        Route::post('/reset-group', [SettingsController::class, 'resetGroup'])->name('reset-group');
        Route::get('/export', [SettingsController::class, 'export'])->name('export');
        Route::post('/import', [SettingsController::class, 'import'])->name('import');
        Route::post('/clear-cache', [SettingsController::class, 'clearCache'])->name('clear-cache');
        Route::get('/public', [SettingsController::class, 'getPublicSettings'])->name('public');
    });

    // Loyalty Points Management Routes
    Route::prefix('loyalty-points')->name('loyalty-points.')->group(function () {
        Route::get('/', [App\Http\Controllers\Dashboard\LoyaltyPointController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Dashboard\LoyaltyPointController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Dashboard\LoyaltyPointController::class, 'store'])->name('store');
        Route::get('/{loyaltyPoint}', [App\Http\Controllers\Dashboard\LoyaltyPointController::class, 'show'])->name('show');
        Route::get('/{loyaltyPoint}/edit', [App\Http\Controllers\Dashboard\LoyaltyPointController::class, 'edit'])->name('edit');
        Route::put('/{loyaltyPoint}', [App\Http\Controllers\Dashboard\LoyaltyPointController::class, 'update'])->name('update');
        Route::delete('/{loyaltyPoint}', [App\Http\Controllers\Dashboard\LoyaltyPointController::class, 'destroy'])->name('destroy');
        Route::patch('/{loyaltyPoint}/toggle-status', [App\Http\Controllers\Dashboard\LoyaltyPointController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{loyaltyPoint}/mark-expired', [App\Http\Controllers\Dashboard\LoyaltyPointController::class, 'markExpired'])->name('mark-expired');
        Route::post('/add-points', [App\Http\Controllers\Dashboard\LoyaltyPointController::class, 'addPoints'])->name('add-points');
        Route::post('/deduct-points', [App\Http\Controllers\Dashboard\LoyaltyPointController::class, 'deductPoints'])->name('deduct-points');
        Route::get('/export/csv', [App\Http\Controllers\Dashboard\LoyaltyPointController::class, 'export'])->name('export');
        Route::get('/stats', [App\Http\Controllers\Dashboard\LoyaltyPointController::class, 'stats'])->name('stats');
    });

    // Referrals Management Routes
    Route::prefix('referrals')->name('referrals.')->group(function () {
        Route::get('/', [App\Http\Controllers\Dashboard\ReferralController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Dashboard\ReferralController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Dashboard\ReferralController::class, 'store'])->name('store');
        Route::get('/{referral}', [App\Http\Controllers\Dashboard\ReferralController::class, 'show'])->name('show');
        Route::get('/{referral}/edit', [App\Http\Controllers\Dashboard\ReferralController::class, 'edit'])->name('edit');
        Route::put('/{referral}', [App\Http\Controllers\Dashboard\ReferralController::class, 'update'])->name('update');
        Route::delete('/{referral}', [App\Http\Controllers\Dashboard\ReferralController::class, 'destroy'])->name('destroy');
        Route::post('/{referral}/mark-completed', [App\Http\Controllers\Dashboard\ReferralController::class, 'markCompleted'])->name('mark-completed');
        Route::post('/{referral}/cancel', [App\Http\Controllers\Dashboard\ReferralController::class, 'cancel'])->name('cancel');
        Route::get('/generate-code', [App\Http\Controllers\Dashboard\ReferralController::class, 'generateCode'])->name('generate-code');
        Route::get('/export/csv', [App\Http\Controllers\Dashboard\ReferralController::class, 'export'])->name('export');
        Route::get('/stats', [App\Http\Controllers\Dashboard\ReferralController::class, 'stats'])->name('stats');
    });

    // Sliders Management Routes
    Route::prefix('sliders')->name('sliders.')->group(function () {
        Route::get('/', [App\Http\Controllers\Dashboard\SliderController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Dashboard\SliderController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Dashboard\SliderController::class, 'store'])->name('store');
        Route::get('/{slider}', [App\Http\Controllers\Dashboard\SliderController::class, 'show'])->name('show');
        Route::get('/{slider}/edit', [App\Http\Controllers\Dashboard\SliderController::class, 'edit'])->name('edit');
        Route::put('/{slider}', [App\Http\Controllers\Dashboard\SliderController::class, 'update'])->name('update');
        Route::delete('/{slider}', [App\Http\Controllers\Dashboard\SliderController::class, 'destroy'])->name('destroy');
        Route::post('/{slider}/toggle-status', [App\Http\Controllers\Dashboard\SliderController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/update-order', [App\Http\Controllers\Dashboard\SliderController::class, 'updateOrder'])->name('update-order');
    });

    // Carts Management Routes
    Route::prefix('carts')->name('carts.')->group(function () {
        Route::get('/', [App\Http\Controllers\Dashboard\CartController::class, 'index'])->name('index');
        Route::get('/{cart}', [App\Http\Controllers\Dashboard\CartController::class, 'show'])->name('show');
        Route::post('/{cart}/mark-abandoned', [App\Http\Controllers\Dashboard\CartController::class, 'markAsAbandoned'])->name('mark-abandoned');
        Route::post('/{cart}/restore', [App\Http\Controllers\Dashboard\CartController::class, 'restore'])->name('restore');
        Route::delete('/{cart}', [App\Http\Controllers\Dashboard\CartController::class, 'destroy'])->name('destroy');
        Route::get('/export/csv', [App\Http\Controllers\Dashboard\CartController::class, 'export'])->name('export');
        Route::post('/cleanup', [App\Http\Controllers\Dashboard\CartController::class, 'cleanup'])->name('cleanup');
        Route::get('/abandoned/stats', [App\Http\Controllers\Dashboard\CartController::class, 'abandonedStats'])->name('abandoned-stats');
        Route::post('/{cart}/send-notification', [App\Http\Controllers\Dashboard\CartController::class, 'sendNotification'])->name('send-notification');
        Route::post('/send-bulk-notifications', [App\Http\Controllers\Dashboard\CartController::class, 'sendBulkNotifications'])->name('send-bulk-notifications');
        Route::get('/notification-templates', [App\Http\Controllers\Dashboard\CartController::class, 'getNotificationTemplates'])->name('notification-templates');
    });

    // Users Management Routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [App\Http\Controllers\Dashboard\UserController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Dashboard\UserController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Dashboard\UserController::class, 'store'])->name('store');
        Route::get('/{user}', [App\Http\Controllers\Dashboard\UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [App\Http\Controllers\Dashboard\UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [App\Http\Controllers\Dashboard\UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [App\Http\Controllers\Dashboard\UserController::class, 'destroy'])->name('destroy');
        Route::patch('/{user}/toggle-status', [App\Http\Controllers\Dashboard\UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{user}/reset-password', [App\Http\Controllers\Dashboard\UserController::class, 'resetPassword'])->name('reset-password');
        Route::get('/{user}/sessions', [App\Http\Controllers\Dashboard\UserController::class, 'sessions'])->name('sessions');
        Route::post('/{user}/terminate-all-sessions', [App\Http\Controllers\Dashboard\UserController::class, 'terminateAllSessions'])->name('terminate-all-sessions');
        Route::post('/sessions/{session}/terminate', [App\Http\Controllers\Dashboard\UserController::class, 'terminateSession'])->name('terminate-session');
        Route::get('/export/csv', [App\Http\Controllers\Dashboard\UserController::class, 'export'])->name('export');
    });

    // Categories Management Routes
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [App\Http\Controllers\Dashboard\CategoryController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Dashboard\CategoryController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Dashboard\CategoryController::class, 'store'])->name('store');
        Route::get('/{category}', [App\Http\Controllers\Dashboard\CategoryController::class, 'show'])->name('show');
        Route::get('/{category}/edit', [App\Http\Controllers\Dashboard\CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [App\Http\Controllers\Dashboard\CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [App\Http\Controllers\Dashboard\CategoryController::class, 'destroy'])->name('destroy');
        Route::patch('/{category}/toggle-status', [App\Http\Controllers\Dashboard\CategoryController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/update-sort-order', [App\Http\Controllers\Dashboard\CategoryController::class, 'updateSortOrder'])->name('update-sort-order');
        Route::get('/export/csv', [App\Http\Controllers\Dashboard\CategoryController::class, 'export'])->name('export');
    });

    // Products Management Routes
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [App\Http\Controllers\Dashboard\ProductController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Dashboard\ProductController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Dashboard\ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [App\Http\Controllers\Dashboard\ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [App\Http\Controllers\Dashboard\ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [App\Http\Controllers\Dashboard\ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [App\Http\Controllers\Dashboard\ProductController::class, 'destroy'])->name('destroy');
        Route::patch('/{product}/toggle-status', [App\Http\Controllers\Dashboard\ProductController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('/{product}/toggle-featured', [App\Http\Controllers\Dashboard\ProductController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/{product}/update-stock', [App\Http\Controllers\Dashboard\ProductController::class, 'updateStock'])->name('update-stock');
        Route::get('/export/csv', [App\Http\Controllers\Dashboard\ProductController::class, 'export'])->name('export');
    });

    // Digital Cards Management Routes
    Route::prefix('digital-cards')->name('digital-cards.')->group(function () {
        Route::get('/', [App\Http\Controllers\Dashboard\DigitalCardController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Dashboard\DigitalCardController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Dashboard\DigitalCardController::class, 'store'])->name('store');
        Route::get('/{digitalCard}', [App\Http\Controllers\Dashboard\DigitalCardController::class, 'show'])->name('show');
        Route::get('/{digitalCard}/edit', [App\Http\Controllers\Dashboard\DigitalCardController::class, 'edit'])->name('edit');
        Route::put('/{digitalCard}', [App\Http\Controllers\Dashboard\DigitalCardController::class, 'update'])->name('update');
        Route::delete('/{digitalCard}', [App\Http\Controllers\Dashboard\DigitalCardController::class, 'destroy'])->name('destroy');
        Route::patch('/{digitalCard}/toggle-status', [App\Http\Controllers\Dashboard\DigitalCardController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{digitalCard}/mark-used', [App\Http\Controllers\Dashboard\DigitalCardController::class, 'markAsUsed'])->name('mark-used');
        Route::post('/generate-bulk', [App\Http\Controllers\Dashboard\DigitalCardController::class, 'generateBulk'])->name('generate-bulk');
        Route::get('/export/csv', [App\Http\Controllers\Dashboard\DigitalCardController::class, 'export'])->name('export');
    });

    // Roles Management Routes
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [App\Http\Controllers\Dashboard\RoleController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Dashboard\RoleController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Dashboard\RoleController::class, 'store'])->name('store');
        Route::get('/{role}', [App\Http\Controllers\Dashboard\RoleController::class, 'show'])->name('show');
        Route::get('/{role}/edit', [App\Http\Controllers\Dashboard\RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [App\Http\Controllers\Dashboard\RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [App\Http\Controllers\Dashboard\RoleController::class, 'destroy'])->name('destroy');
        Route::patch('/{role}/toggle-status', [App\Http\Controllers\Dashboard\RoleController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{role}/duplicate', [App\Http\Controllers\Dashboard\RoleController::class, 'duplicate'])->name('duplicate');
        Route::get('/export/csv', [App\Http\Controllers\Dashboard\RoleController::class, 'export'])->name('export');
    });

    // Permissions Management Routes
    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Dashboard\PermissionController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Dashboard\PermissionController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Dashboard\PermissionController::class, 'store'])->name('store');
        Route::get('/{permission}', [App\Http\Controllers\Dashboard\PermissionController::class, 'show'])->name('show');
        Route::get('/{permission}/edit', [App\Http\Controllers\Dashboard\PermissionController::class, 'edit'])->name('edit');
        Route::put('/{permission}', [App\Http\Controllers\Dashboard\PermissionController::class, 'update'])->name('update');
        Route::delete('/{permission}', [App\Http\Controllers\Dashboard\PermissionController::class, 'destroy'])->name('destroy');
        Route::patch('/{permission}/toggle-status', [App\Http\Controllers\Dashboard\PermissionController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{permission}/duplicate', [App\Http\Controllers\Dashboard\PermissionController::class, 'duplicate'])->name('duplicate');
        Route::post('/bulk-create', [App\Http\Controllers\Dashboard\PermissionController::class, 'bulkCreate'])->name('bulk-create');
        Route::get('/export/csv', [App\Http\Controllers\Dashboard\PermissionController::class, 'export'])->name('export');
    });
});

// AmwalPay Routes (Public)
Route::get('/amwalpay/process', [\App\Http\Controllers\AmwalPayController::class, 'process'])->name('amwal.process');
Route::match(['GET','POST'],'/amwalpay/callback', [\App\Http\Controllers\AmwalPayController::class, 'callBack'])->name('amwal.callback');

