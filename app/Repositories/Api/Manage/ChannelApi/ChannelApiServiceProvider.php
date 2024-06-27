<?php

namespace App\Repositories\Api\Manage\ChannelApi;

class ChannelApiServiceProvider
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
        $this->app->bind(ChannelApiInterface::class, ChannelApiRepository::class);
    }
}
