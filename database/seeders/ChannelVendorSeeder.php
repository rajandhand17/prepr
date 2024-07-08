<?php

namespace Database\Seeders;

use App\Models\ChannelVendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ChannelVendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'vendor_name'  => 'Magnet',
                'vendor_email' => null,
                //                'api_key'      => 'learnlab-'.md5('Magnet').'-'.Str::random(30),
                //                'secret_key'   => Str::random(30),
                'api_key'          => 'learnlab-6c72aa4de620ddfe18b992ae82c0d92f-f9UjZB2di55YQt8ZOe3BN2BQjGPfBA',
                'secret_key'       => 'A4tSdtPHvLcipMZ4lOzsc7V9nWF0W5',
            ],
        ];

        foreach ($data as $item) {
            ChannelVendor::query()->firstOrCreate([
                'vendor_name' => $item['vendor_name'],
            ], $item);
        }
    }
}
