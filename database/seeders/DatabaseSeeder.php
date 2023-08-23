<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            SocialConnectTableSeeder::class,
            PermissionSeeder::class,
            RolesSeeder::class,
            AssignPermissionToRolesSeeder::class,
            EmailTemplateSeeder::class,
            FlexibleDateDurationSeeder::class,
            LevelsSeeder::class,
            DurationsSeeder::class,
        ]);
    }
}
