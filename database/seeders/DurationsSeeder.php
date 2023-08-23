<?php

namespace Database\Seeders;

use App\Models\Duration;
use Illuminate\Database\Seeder;

class DurationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $duration = [
            [
                'title'      => 'Less than 2 hours',
                'fr_CA_title'=> 'Moins de 2 heures',
            ], [
                'title'      => '2 -4 hours',
                'fr_CA_title'=> '24 heures',
            ], [
                'title'      => '4 -8 hours',
                'fr_CA_title'=> '48 heures',
            ], [
                'title'      => '1 -2 Days',
                'fr_CA_title'=> '12 jours',
            ], [
                'title'      => '3 -5 Days',
                'fr_CA_title'=> '3 à 5 jours',
            ], [
                'title'      => '5+ Days',
                'fr_CA_title'=> '5+ jours',
            ],
        ];
        foreach ($duration as $durations) {
            Duration::updateOrCreate([
                'title'      => $durations['title'],
                'fr_CA_title'=> $durations['fr_CA_title'],
            ], [
                'title'      => $durations['title'],
                'fr_CA_title'=> $durations['fr_CA_title'],
            ]);
        }
    }
}
