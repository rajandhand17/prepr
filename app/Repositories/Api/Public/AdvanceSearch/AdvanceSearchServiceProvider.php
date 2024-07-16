<?php

namespace App\Repositories\Api\Public\AdvanceSearch;

use Illuminate\Support\ServiceProvider;

class AdvanceSearchServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Public\AdvanceSearch\AdvanceSearchInterface', 'App\Repositories\Api\Public\AdvanceSearch\AdvanceSearchRepository');
    }
}
