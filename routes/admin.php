<?php

use App\Http\Controllers\Admin\CashierController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductPackageController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login.post');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/admin/logout', [LogoutController::class, 'logout'])->name('admin.logout');
    
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/orders/create', [DashboardController::class, 'createOrder'])->name('admin.orders.create');
    Route::put('/admin/orders/{order}/quick-status', [DashboardController::class, 'quickUpdateStatus'])->name('admin.orders.quickStatus');
    Route::put('/admin/orders/{order}/quick-payment', [DashboardController::class, 'quickConfirmPayment'])->name('admin.orders.quickPayment');
    
    Route::get('/admin/cashier', [CashierController::class, 'index'])->name('admin.cashier.index');
    Route::post('/admin/cashier', [CashierController::class, 'store'])->name('admin.cashier.store');
    Route::get('/admin/cashier/receipt/{order}', [CashierController::class, 'showReceipt'])->name('admin.cashier.receipt');
    
    Route::resource('admin/categories', CategoryController::class)->names([
        'index' => 'admin.categories.index',
        'create' => 'admin.categories.create',
        'store' => 'admin.categories.store',
        'show' => 'admin.categories.show',
        'edit' => 'admin.categories.edit',
        'update' => 'admin.categories.update',
        'destroy' => 'admin.categories.destroy',
    ]);
    
    Route::resource('admin/products', ProductController::class)->names([
        'index' => 'admin.products.index',
        'create' => 'admin.products.create',
        'store' => 'admin.products.store',
        'show' => 'admin.products.show',
        'edit' => 'admin.products.edit',
        'update' => 'admin.products.update',
        'destroy' => 'admin.products.destroy',
    ]);
    
    Route::get('/admin/products/stock/manage', [ProductController::class, 'stock'])->name('admin.products.stock');
    Route::post('/admin/products/stock/update', [ProductController::class, 'updateStock'])->name('admin.products.stock.update');
    
    Route::resource('admin/packages', ProductPackageController::class)->names([
        'index' => 'admin.packages.index',
        'create' => 'admin.packages.create',
        'store' => 'admin.packages.store',
        'show' => 'admin.packages.show',
        'edit' => 'admin.packages.edit',
        'update' => 'admin.packages.update',
        'destroy' => 'admin.packages.destroy',
    ]);
    
    Route::get('/admin/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/admin/orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::put('/admin/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::put('/admin/orders/{order}/payment', [OrderController::class, 'confirmPayment'])->name('admin.orders.confirmPayment');
    
    Route::resource('admin/payment-methods', PaymentMethodController::class)->names([
        'index' => 'admin.payment-methods.index',
        'create' => 'admin.payment-methods.create',
        'store' => 'admin.payment-methods.store',
        'show' => 'admin.payment-methods.show',
        'edit' => 'admin.payment-methods.edit',
        'update' => 'admin.payment-methods.update',
        'destroy' => 'admin.payment-methods.destroy',
    ]);
});

