<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require_once __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

//        Artisan::call('db:wipe');
//        Artisan::call('migrate');
//        Artisan::call('db:seed');
//        Artisan::call('migrate-old-data:achievement-condition-list');
//        Artisan::call('migrate-old-data:categories');
//        Artisan::call('migrate-old-data:host');
//        Artisan::call('migrate-old-data:languages');
//        Artisan::call('migrate-old-data:pitch-template');
//        Artisan::call('migrate-old-data:project-industry');
//        Artisan::call('migrate-old-data:project-stages');
//        Artisan::call('migrate-old-data:project-status');
//        Artisan::call('migrate-old-data:project-type');
//        Artisan::call('migrate-old-data:project-verticals');
//        Artisan::call('migrate-old-data:ranks');
//        Artisan::call('migrate-old-data:social-link');
//        Artisan::call('migrate-old-data:tags');
//        Artisan::call('migrate-old-data:tag-groups');
//        Artisan::call('migrate-old-data:skills');
//        Artisan::call('migrate-old-data:skill-stacks');
//        Artisan::call('migrate-old-data:skill-groups');
//        Artisan::call('passport:install');
//        Artisan::call('optimize:clear');

        return $app;
    }
}
