<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            //for user:
            Route::middleware('api')
                ->prefix('api/user')   // it's in url. they put it just: 'api', and i think it should be:'api/user'.
                ->group(base_path('routes/user.php'));

            //for admin:
            Route::middleware('api')
                ->prefix('api/admin')    //the same thing!
                ->group(base_path('routes/admin.php'));

            //for doctor:
            Route::middleware('api')
                ->prefix('api')    //the same thing!
                ->group(base_path('routes/doctor.php'));

            //for hotel:
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/hotel.php'));

            //for airline:
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/airline.php'));

            //for trip_company:
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/trip.php'));

            //for attraction:
            Route::middleware('api')
                ->prefix('api/attraction')
                ->group(base_path('routes/attraction.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));


        });
    }
}
