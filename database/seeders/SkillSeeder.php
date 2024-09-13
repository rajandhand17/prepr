<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            [
                'title'       => 'Critical thinking',
                'fr_CA_title' => 'Esprit critique',
            ],
            [
                'title'       => 'Communication',
                'fr_CA_title' => 'Communication',
            ],
            [
                'title'       => 'Empathy',
                'fr_CA_title' => 'Empathie',
            ],
            [
                'title'       => 'Problem Solving',
                'fr_CA_title' => 'Résolution de problème',
            ],
            [
                'title'       => 'Creativity',
                'fr_CA_title' => 'Créativité',
            ],
        ];

        foreach ($skills as $skill) {
            Skill::updateOrCreate(['title' => $skill['title']], $skill);
        }
    }
}
