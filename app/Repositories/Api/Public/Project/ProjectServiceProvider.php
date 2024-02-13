<?php

namespace App\Repositories\Api\Public\Project;

use Illuminate\Support\ServiceProvider;

class ProjectServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Public\Project\ProjectInterface', 'App\Repositories\Api\Public\Project\ProjectRepository');
    }
}
