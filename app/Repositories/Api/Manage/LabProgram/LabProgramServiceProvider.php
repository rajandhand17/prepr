<?php

namespace App\Repositories\Api\Manage\LabProgram;

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
        $this->app->bind('App\Repositories\Api\Manage\LabProgram\LabProgramInterface', 'App\Repositories\Api\Manage\LabProgram\LabProgramRepository');
    }
}
