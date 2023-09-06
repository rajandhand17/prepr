<?php

namespace App\Repositories\Api\Public\LabProgram;

use Illuminate\Support\ServiceProvider;

class LabProgramServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Public\LabProgram\LabProgramInterface', 'App\Repositories\Api\Public\LabProgram\LabProgramRepository');
    }
}
