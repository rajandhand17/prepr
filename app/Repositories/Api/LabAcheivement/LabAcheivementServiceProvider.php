<?php

namespace App\Repositories\Api\LabAcheivement;

use Illuminate\Support\ServiceProvider;

class LabAcheivementServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\LabAcheivement\LabAcheivementInterface', 'App\Repositories\Api\LabAcheivement\LabAcheivementRepository');
    }
}
