<?php

namespace App\Repositories\Api\Public\Scorm\ScormTracking;

use Illuminate\Support\ServiceProvider;

class ScormTrackingServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Public\Scorm\ScormTracking\ScormTrackingInterface', 'App\Repositories\Api\Public\Scorm\ScormTracking\ScormTrackingRepository');
    }
}
