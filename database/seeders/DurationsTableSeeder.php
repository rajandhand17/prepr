<?php

namespace Database\Seeders;

use App\Models\Duration;
use Illuminate\Database\Seeder;

class DurationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $durations = [
            ['title' => 'Less than 2 hours', 'min_minutes' => null, 'max_minutes' => 120],
            ['title' => '2 -4 hours', 'min_minutes' => 121, 'max_minutes' => 240],
            ['title' => '4 -8 hours', 'min_minutes' => 241, 'max_minutes' => 480],
            ['title' => '1 -2 Days', 'min_minutes' => 1441, 'max_minutes' => 2880],
            ['title' => '3 -5 Days', 'min_minutes' => 4321, 'max_minutes' => 7200],
            ['title' => '5+ Days', 'min_minutes' => 7201, 'max_minutes' => null],
        ];

        foreach ($durations as $duration) {
            Duration::updateOrCreate(
                ['title' => $duration['title']],
                [
                    'min_minutes' => $duration['min_minutes'],
                    'max_minutes' => $duration['max_minutes'],
                ]
            );
        }
    }
}
