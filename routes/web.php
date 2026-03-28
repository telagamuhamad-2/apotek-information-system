<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductIncomingController;
use App\Http\Controllers\ProductOutgoingController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Product Types (Owner only)
    Route::middleware('role:owner')->group(function () {
        Route::resource('product-types', ProductTypeController::class)->parameters([
            'product-types' => 'id'
        ]);
    });

    // Products (Owner only)
    Route::middleware('role:owner')->group(function () {
        Route::resource('products', ProductController::class)->except(['show']);
        Route::post('/products/{id}/update-selling-price', [ProductController::class, 'updateSellingPrice'])
            ->name('products.update-selling-price');
        Route::post('/products/{id}/update-purchase-price', [ProductController::class, 'updatePurchasePrice'])
            ->name('products.update-purchase-price');
        Route::get('/products/export', [ProductController::class, 'export'])
            ->name('products.export');
    });

    // Purchases/Pembelian (Owner only)
    Route::middleware('role:owner')->group(function () {
        Route::get('/pembelian', [ProductIncomingController::class, 'index'])->name('pembelian.index');
        Route::get('/pembelian/create', [ProductIncomingController::class, 'create'])->name('pembelian.create');
        Route::post('/pembelian', [ProductIncomingController::class, 'store'])->name('pembelian.store');
        Route::get('/pembelian/{id}/edit', [ProductIncomingController::class, 'edit'])->name('pembelian.edit');
        Route::put('/pembelian/{id}', [ProductIncomingController::class, 'update'])->name('pembelian.update');
        Route::delete('/pembelian/{id}', [ProductIncomingController::class, 'destroy'])->name('pembelian.destroy');
        Route::get('/pembelian/export', [ProductIncomingController::class, 'export'])
            ->name('pembelian.export');
    });

    // Sales/Penjualan (Owner & Pegawai)
    Route::prefix('penjualan')->group(function () {
        Route::get('/', [ProductOutgoingController::class, 'index'])->name('penjualan.index');
        Route::get('/create', [ProductOutgoingController::class, 'create'])->name('penjualan.create');
        Route::post('/', [ProductOutgoingController::class, 'store'])->name('penjualan.store');
        Route::get('/{id}/edit', [ProductOutgoingController::class, 'edit'])->name('penjualan.edit');
        Route::put('/{id}', [ProductOutgoingController::class, 'update'])->name('penjualan.update');
        Route::delete('/{id}', [ProductOutgoingController::class, 'destroy'])->name('penjualan.destroy');
        Route::get('/export', [ProductOutgoingController::class, 'export'])->name('penjualan.export');

        // AJAX routes for penjualan
        Route::get('/get-product-details', [ProductOutgoingController::class, 'getProductDetails'])
            ->name('penjualan.get-product-details');
        Route::get('/check-stock', [ProductOutgoingController::class, 'checkStock'])
            ->name('penjualan.check-stock');
    });

    // User Management (Owner only)
    Route::middleware('role:owner')->group(function () {
        Route::resource('users', UserController::class);
    });
});
