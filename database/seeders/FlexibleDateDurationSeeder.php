<?php

namespace Database\Seeders;

use App\Models\FlexibleExpireDateDuration;
use Illuminate\Database\Seeder;

class FlexibleDateDurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $durations = [
            [
                'title'             => 'Days',
                'fr_CA_title'       => 'Jours',
            ],
            [
                'title'             => 'Weeks',
                'fr_CA_title'       => 'Semaines',
            ],
            [
                'title'             => 'Months',
                'fr_CA_title'       => 'Mois',
            ],

        ];

        foreach ($durations as $duration) {
            FlexibleExpireDateDuration::updateOrCreate([
                'title' => $duration['title'],
            ], [
                'fr_CA_title' => $duration['fr_CA_title'],
            ]);
        }
    }
}
