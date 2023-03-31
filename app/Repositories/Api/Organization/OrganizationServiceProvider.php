<?php
namespace App\Repositories\Api\Organization;


use Illuminate\Support\ServiceProvider;


class OrganizationServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Organization\OrganizationInterface', 'App\Repositories\Api\Organization\OrganizationRepository');
    }
}
