<?php

namespace Database\Seeders;

use App\Models\ChannelApis;
use Illuminate\Database\Seeder;

class ChannelApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'api_name' => 'Get Labs',
                'api_slug' => 'get-labs',
            ],
            [
                'api_name' => 'Assign User to Lab',
                'api_slug' => 'assign-user-to-lab',
            ],
            [
                'api_name' => 'Get Challenges',
                'api_slug' => 'get-challenges',
            ],
        ];
        foreach ($data as $item) {
            ChannelApis::updateOrCreate(['api_name' => $item['api_name']], $item);
        }
    }
}
