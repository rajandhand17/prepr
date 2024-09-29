<?php

namespace Database\Seeders;

use App\Models\Levels;
use Illuminate\Database\Seeder;

class LevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            [
                'title'      => 'Beginner',
                'fr_CA_title'=> 'DEBUTANT',
            ], [
                'title'      => 'Intermediate',
                'fr_CA_title'=> 'intermédiaire',
            ], [
                'title'      => 'Advanced',
                'fr_CA_title'=> 'avancé',
            ], [
                'title'      => 'Mixed',
                'fr_CA_title'=> 'Mixed',
            ],
        ];
        foreach ($levels as $level) {
            Levels::updateOrCreate([
                'title'      => $level['title'],
                'fr_CA_title'=> $level['fr_CA_title'],
            ], [
                'title'      => $level['title'],
                'fr_CA_title'=> $level['fr_CA_title'],
            ]);
        }
    }
}
