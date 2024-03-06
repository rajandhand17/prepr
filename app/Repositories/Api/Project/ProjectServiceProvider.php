<?php

namespace App\Repositories\Api\Project;

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
    public function register(): void
    {
        $this->app->bind('App\Repositories\Api\Project\ProjectInterface', 'App\Repositories\Api\Project\ProjectRepository');
    }
}
