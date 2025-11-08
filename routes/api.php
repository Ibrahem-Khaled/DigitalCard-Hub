<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\CouponController;
use App\Http\Controllers\Api\V1\LoyaltyPointController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\AIChatController;
use App\Http\Controllers\Api\V1\SliderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Version 1
Route::prefix('v1')->name('api.v1.')->group(function () {
    
    // Public Routes
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
        Route::post('/verify', [AuthController::class, 'verify'])->name('verify');
        Route::post('/resend-verification', [AuthController::class, 'resendVerification'])->name('resend-verification');
    });

    // Public Product Routes
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('/{product}/reviews', [ProductController::class, 'reviews'])->name('reviews');
        Route::get('/search/{query}', [ProductController::class, 'search'])->name('search');
    });

    // Public Category Routes
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
        Route::get('/{category}/products', [CategoryController::class, 'products'])->name('products');
    });

    // AI Chat (Public)
    Route::prefix('ai-chat')->name('ai-chat.')->group(function () {
        Route::post('/chat', [AIChatController::class, 'chat'])->name('chat');
        Route::get('/product-suggestions', [AIChatController::class, 'getProductSuggestions'])->name('product-suggestions');
    });

    // Sliders Routes (Public)
    Route::prefix('sliders')->name('sliders.')->group(function () {
        Route::get('/', [SliderController::class, 'index'])->name('index');
        Route::get('/homepage', [SliderController::class, 'homepage'])->name('homepage');
        Route::get('/position/{position}', [SliderController::class, 'byPosition'])->name('by-position');
        Route::get('/{id}', [SliderController::class, 'show'])->name('show');
    });

    // Protected Routes (Require Authentication)
    Route::middleware('auth:sanctum')->group(function () {
        
        // Auth Routes (Authenticated)
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/me', [AuthController::class, 'me'])->name('me');
            Route::put('/profile', [AuthController::class, 'updateProfile'])->name('update-profile');
            Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password');
            Route::post('/refresh-token', [AuthController::class, 'refreshToken'])->name('refresh-token');
        });

        // Cart Routes
        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [CartController::class, 'index'])->name('index');
            Route::post('/add', [CartController::class, 'add'])->name('add');
            Route::put('/{cartItem}', [CartController::class, 'update'])->name('update');
            Route::delete('/{cartItem}', [CartController::class, 'remove'])->name('remove');
            Route::post('/clear', [CartController::class, 'clear'])->name('clear');
            Route::post('/apply-coupon', [CartController::class, 'applyCoupon'])->name('apply-coupon');
            Route::delete('/remove-coupon', [CartController::class, 'removeCoupon'])->name('remove-coupon');
        });

        // Order Routes
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::post('/', [OrderController::class, 'store'])->name('store');
            Route::get('/{order}', [OrderController::class, 'show'])->name('show');
            Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        });

        // Profile Routes
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
            Route::get('/orders', [ProfileController::class, 'orders'])->name('orders');
            Route::get('/loyalty-points', [ProfileController::class, 'loyaltyPoints'])->name('loyalty-points');
            Route::get('/referrals', [ProfileController::class, 'referrals'])->name('referrals');
        });

        // Coupon Routes
        Route::prefix('coupons')->name('coupons.')->group(function () {
            Route::get('/', [CouponController::class, 'index'])->name('index');
            Route::post('/validate', [CouponController::class, 'validate'])->name('validate');
        });

        // Loyalty Points Routes
        Route::prefix('loyalty-points')->name('loyalty-points.')->group(function () {
            Route::get('/', [LoyaltyPointController::class, 'index'])->name('index');
            Route::get('/transactions', [LoyaltyPointController::class, 'transactions'])->name('transactions');
        });

        // Notifications Routes
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/unread', [NotificationController::class, 'unread'])->name('unread');
            Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
            Route::put('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        });

        // Product Reviews (Authenticated)
        Route::prefix('products')->name('products.')->group(function () {
            Route::post('/{product}/reviews', [ProductController::class, 'storeReview'])->name('store-review');
            Route::put('/reviews/{review}', [ProductController::class, 'updateReview'])->name('update-review');
            Route::delete('/reviews/{review}', [ProductController::class, 'deleteReview'])->name('delete-review');
        });
    });
});


