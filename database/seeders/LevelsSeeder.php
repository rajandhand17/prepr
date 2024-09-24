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
                'title'      => 'Senior',
                'fr_CA_title'=> 'supérieur',
            ], [
                'title'      => 'Advanced',
                'fr_CA_title'=> 'avancé',
            ], [
                'title'      => 'Junior',
                'fr_CA_title'=> 'Junior',
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
