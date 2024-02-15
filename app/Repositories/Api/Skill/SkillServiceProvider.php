<?php

namespace App\Repositories\Api\Skill;

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
        $this->app->bind('App\Repositories\Api\Skill\SkillInterface', 'App\Repositories\Api\Skill\SkillRepository');
    }
}
