<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\EntradaService;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

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
        RateLimiter::for('login', function ($request) {
            $key = ($request->input('usuario') ?? 'guest').'||'.$request->ip();
            return [
                Limit::perMinute(10)->by($key),
            ];
        });

        RateLimiter::for('face', function ($request) {
            return [
                Limit::perMinute(30)->by($request->ip()),
            ];
        });
    }
}
