<?php

use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\MenuController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\StorageController;
use Illuminate\Support\Facades\Route;

// Storage route for serving files without symlink
Route::get('/storage/{path}', [StorageController::class, 'show'])
    ->where('path', '.*')
    ->name('storage');

Route::get('/', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{product}', [MenuController::class, 'show'])->name('menu.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/add-package/{package}', [CartController::class, 'addPackage'])->name('cart.addPackage');
Route::put('/cart/{key}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{key}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', function () {
    $paymentMethods = \App\Models\PaymentMethod::active()->orderBy('display_order')->orderBy('name')->get();
    return view('customer.order.checkout', compact('paymentMethods'));
})->name('orders.checkout');
Route::post('/checkout', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{orderCode}', [OrderController::class, 'show'])->name('orders.show');
