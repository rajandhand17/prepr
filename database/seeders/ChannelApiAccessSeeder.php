<?php

namespace Database\Seeders;

use App\Models\ChannelApis;
use App\Models\ChannelVendor;
use Illuminate\Database\Seeder;

class ChannelApiAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // For Now give access to all api for all vendor
        $vendors = ChannelVendor::all();
        foreach ($vendors as $vendor) {
            $vendor->channelApis()->sync(ChannelApis::all());
        }
    }
}
