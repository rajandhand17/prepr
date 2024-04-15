<?php

namespace App\Repositories\Api\Scorm;

use Illuminate\Support\ServiceProvider;

class ScormServiceProvider  extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Scorm\ScormInterface', 'App\Repositories\Api\Scorm\ScormRepository');
    }
}
