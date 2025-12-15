<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema; // <-- ADD THIS LINE
use Illuminate\Support\ServiceProvider;

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
        // ADD THE MIGRATION FIX HERE
        Schema::defaultStringLength(191); 

        // Your existing production environment logic
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}