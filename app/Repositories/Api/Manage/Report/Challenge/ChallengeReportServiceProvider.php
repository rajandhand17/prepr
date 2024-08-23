<?php

namespace App\Repositories\Api\Manage\Report\Challenge;

use Illuminate\Support\ServiceProvider;

class ChallengeReportServiceProvider extends ServiceProvider
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
        $this->app->bind(
            'App\Repositories\Api\Manage\Report\Challenge\ChallengeReportInterface',
            'App\Repositories\Api\Manage\Report\Challenge\ChallengeReportRepository'
        );
    }
}
