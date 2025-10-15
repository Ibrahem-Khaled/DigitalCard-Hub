<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard Routes
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Products Routes
    Route::get('/products', function () {
        return view('dashboard.products');
    })->name('products');

    // Orders Routes
    Route::get('/orders', function () {
        return view('dashboard.orders');
    })->name('orders');

    // Customers Routes
    Route::get('/customers', function () {
        return view('dashboard.customers');
    })->name('customers');
});

// Redirect dashboard to dashboard.index
Route::get('/dashboard', function () {
    return redirect()->route('dashboard.index');
})->middleware('auth');
