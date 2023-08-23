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
                'title'      => 'Beginner Level',
                'fr_CA_title'=> 'Niveau DEBUTANT',
            ], [
                'title'      => 'Intermediate Level',
                'fr_CA_title'=> 'Niveau intermédiaire',
            ], [
                'title'      => 'Senior Level',
                'fr_CA_title'=> 'Niveau supérieur',
            ], [
                'title'      => 'Advanced Level',
                'fr_CA_title'=> 'Niveau avancé',
            ], [
                'title'      => 'Junior Level',
                'fr_CA_title'=> 'Niveau Junior',
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
