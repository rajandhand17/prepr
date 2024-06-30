<?php

namespace App\Repositories\Api\StartPage;

use Illuminate\Support\ServiceProvider;

class StartPageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Repositories\Api\StartPage\StartPageInterface', 'App\Repositories\Api\StartPage\StartPageRepository');
    }
}
