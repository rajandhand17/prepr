<?php

namespace Database\Seeders;

use App\Models\ProjectAccessLevel;
use Illuminate\Database\Seeder;

class ProjectAccessLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $access_level =
        [
            [
                'name'         => 'team_leader',
                'display_name' => 'Team Leader',
                'description'  => 'Team leader of Project, who invite manage the Project and has all the permissions',
            ],
            [
                'name'         => 'editor',
                'display_name' => 'Editor',
                'description'  => 'Editor manages the Project.',
            ], [
                'name'         => 'viewer',
                'display_name' => 'Viewer',
                'description'  => 'Viewer can view the project but cannot manage the project',
            ],
        ];
        foreach ($access_level as $key => $access) {
            ProjectAccessLevel::updateOrCreate(
                [
                    'name'         => $access['name'],
                    'display_name' => $access['display_name'],
                    'description'  => $access['description'],
                ],
                [
                    'name'         => $access['name'],
                    'display_name' => $access['display_name'],
                    'description'  => $access['description'],
                ],
            );
        }
    }
}
