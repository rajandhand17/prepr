<?php

namespace App\Repositories\Api\Public\Skill;

use Illuminate\Support\ServiceProvider;

class SkillServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Public\Skill\SkillInterface', 'App\Repositories\Api\Public\Skill\SkillRepository');
    }
}
