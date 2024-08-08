<?php

namespace App\Repositories\Api\ComponentAssociation;

use Illuminate\Support\ServiceProvider;

class ComponentAssociationServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\ComponentAssociation\ComponentAssociationInterface', 'App\Repositories\Api\ComponentAssociation\ComponentAssociationRepository');
    }
}
