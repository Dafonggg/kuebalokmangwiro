<?php

use App\Http\Controllers\Kitchen\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'kitchen'])->group(function () {
    Route::get('/kitchen/orders', [OrderController::class, 'index'])->name('kitchen.orders.index');
    Route::put('/kitchen/orders/{order}/ready', [OrderController::class, 'markReady'])->name('kitchen.orders.ready');
});

