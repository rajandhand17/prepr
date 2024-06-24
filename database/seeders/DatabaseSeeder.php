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
        $classes = [
            SocialConnectTableSeeder::class,
            PermissionSeeder::class,
            RolesSeeder::class,
            AssignPermissionToRolesSeeder::class,
            EmailTemplateSeeder::class,
            FlexibleDateDurationSeeder::class,
            LevelsSeeder::class,
            DurationsSeeder::class,
            ProjectSubmissionRequirementTableSeeder::class,
            ChallengeAnnouncementRecipientSeeder::class,
            ProjectAccessLevelSeeder::class,
            DurationsTableSeeder::class,
            BusinessChallengeTacklingSeeder::class,
            ChannelApiSeeder::class,
            ChannelVendorSeeder::class,
            ChannelApiAccessSeeder::class,
        ];

        if (app()->environment('testing')) {
            $classes[] = UserSeeder::class;
            $classes[] = LanguageSeeder::class;
            $classes[] = SkillSeeder::class;
            $classes[] = TagSeeder::class;
            $classes[] = ChannelApiOrganizationSeeder::class;
        }

        $this->call($classes);
    }
}
