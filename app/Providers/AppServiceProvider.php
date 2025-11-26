<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StorageController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure storage route is registered even if route cache is stale
        // This is a backup in case route cache doesn't include the storage route
        if (!Route::has('storage')) {
            Route::get('/storage/{path}', [StorageController::class, 'show'])
                ->where('path', '[a-zA-Z0-9\/\._-]+')
                ->name('storage');
        }
    }
}
