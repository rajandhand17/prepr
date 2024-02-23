<?php

namespace App\Repositories\Api\Discussion;

use Illuminate\Support\ServiceProvider;

class DiscussionServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Discussion\DiscussionInterface', 'App\Repositories\Api\Discussion\DiscussionRepository');
    }
}
