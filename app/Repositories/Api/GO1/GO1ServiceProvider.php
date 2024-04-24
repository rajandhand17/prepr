<?php

namespace App\Repositories\Api\GO1;

use Carbon\Laravel\ServiceProvider;

class GO1ServiceProvider extends ServiceProvider
{
    public function boot()
    {
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->app->bind(GO1Interface::class, GO1Repository::class);
    }
}
