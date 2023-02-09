<?php

namespace Database\Seeders;

use App\Models\SocialConnect;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SocialConnectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $social_connect_list = [
            [
                'name' => 'facebook',
                'logo' => 'https://cdn-icons-png.flaticon.com/512/124/124010.png'
            ],
            [
                'name' => 'instagram',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e7/Instagram_logo_2016.svg/2048px-Instagram_logo_2016.svg.png'
            ],
            [
                'name' => 'microsoft',
                'logo' => 'https://cdn-icons-png.flaticon.com/512/732/732221.png'
            ],
            [
                'name' => 'twitter',
                'logo' => 'https://cdn-icons-png.flaticon.com/512/124/124021.png'
            ],
            [
                'name' => 'linkedin',
                'logo' => 'https://cdn-icons-png.flaticon.com/512/174/174857.png'
            ],
            [
                'name' => 'google',
                'logo' => 'https://www.incidentiq.com/wp-content/uploads/2022/09/GoogleSSO-logo.png'
            ],
            [
                'name' => 'apple',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Apple_logo_black.svg/1667px-Apple_logo_black.svg.png'
            ],
            [
                'name' => 'magnet',
                'logo' => 'https://bfrc.magnet.today/wp-content/uploads/2022/01/magnet-icon-rgb.png'
            ],
        ];

        foreach ($social_connect_list as $key => $single_social_conect){
            SocialConnect::updateOrCreate(
                ['name' =>  $single_social_conect['name']],
                ['logo' => $single_social_conect['logo']]
            );
        }
    }
}
