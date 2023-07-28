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
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();
        $this->routes(function () {
            Route::middleware('api')
                    ->prefix('api')
                    ->group(base_path('routes/api.php'));

            Route::middleware('web')->group(base_path('routes/web.php'));
            Route::prefix('api/v1/master/')->middleware('api')->group(base_path('routes/v1/master.php'));
            Route::prefix('api/v1/auth/')->middleware('api')->group(base_path('routes/v1/auth.php'));
            Route::prefix('api/v1/manage/organization/')->middleware('api')->group(base_path('routes/v1/manage/organization.php'));
            Route::prefix('api/v1/manage/member-management/')->middleware('api')->group(base_path('routes/v1/manage/member-management.php'));
            Route::prefix('api/v1/manage/lab/')->middleware('api')->group(base_path('routes/v1/manage/lab.php'));
            Route::prefix('api/v1/user/')->middleware('api')->group(base_path('routes/v1/user.php'));
            Route::prefix('api/v1/public/organization/')->middleware('api')->group(base_path('routes/v1/public/organization.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
