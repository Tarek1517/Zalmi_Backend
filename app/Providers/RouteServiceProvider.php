<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use PHPUnit\Event\Code\Throwable;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot()
    {
        RateLimiter::for('api', function (Request $request) {
            Limit::perMinute(env("RATE_LIMIT_PER_MINUTES"))->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api/vendor.php'));


            // Add Sanctum CSRF route here
            Route::middleware('api')
                ->group(function () {
                    Route::get('/sanctum/csrf-cookie', [\Laravel\Sanctum\Http\Controllers\CsrfCookieController::class, 'show']);
                });

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
