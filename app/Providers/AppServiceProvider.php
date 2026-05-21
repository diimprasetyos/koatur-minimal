<?php

namespace App\Providers;

use App\Http\Middleware\SubscriptionActive;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(Router $router): void
    {

        if (env('FORCE_HTTPS')) {
            \URL::forceScheme('https');
        }
        // bypass super_admin
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('super_admin')) {
                return true;
            }
        });

        $router->aliasMiddleware('subscription.active', SubscriptionActive::class);
    }
}
