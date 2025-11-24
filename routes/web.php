<?php

use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\MenuController;
use App\Http\Controllers\Customer\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{product}', [MenuController::class, 'show'])->name('menu.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/{productId}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{productId}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', function () {
    $paymentMethods = \App\Models\PaymentMethod::active()->orderBy('display_order')->orderBy('name')->get();
    return view('customer.order.checkout', compact('paymentMethods'));
})->name('orders.checkout');
Route::post('/checkout', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{orderCode}', [OrderController::class, 'show'])->name('orders.show');
