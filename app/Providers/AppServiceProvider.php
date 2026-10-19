<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        Sanctum::getAccessTokenFromRequestUsing(function ($request) {
            $token = $request->bearerToken();

            if (!$token) {
                $authHeader = $request->header('Authorization')
                    ?? $request->server('HTTP_AUTHORIZATION')
                    ?? $request->server('REDIRECT_HTTP_AUTHORIZATION');

                if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
                    $token = substr($authHeader, 7);
                }
            }

            return $token;
        });
    }
}
