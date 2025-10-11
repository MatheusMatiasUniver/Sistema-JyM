<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\EntradaService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(EntradaService::class, function ($app) {
            return new EntradaService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
