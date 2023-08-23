<?php

namespace Database\Seeders;

use App\Models\Duration;
use Illuminate\Database\Seeder;

class DurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $duration = [
            [
                'title'      => '0 - 14 Days',
                'fr_CA_title'=> '0 - 14 jours',
            ], [
                'title'      => '0 - 1 Month',
                'fr_CA_title'=> '0 - 1 mois',
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
