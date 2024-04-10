<?php

namespace Database\Seeders;

use App\Models\SocialConnect;
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
                'title' => 'facebook',
                'logo'  => 'https://cdn-icons-png.flaticon.com/512/124/124010.png',
            ],
            [
                'title' => 'facebook',
                'logo'  => 'https://cdn-icons-png.flaticon.com/512/124/124010.png',
            ], [
                'title' => 'facebook',
                'logo'  => 'https://cdn-icons-png.flaticon.com/512/124/124010.png',
            ], [
                'title' => 'facebook',
                'logo'  => 'https://cdn-icons-png.flaticon.com/512/124/124010.png',
            ], [
                'title' => 'facebook',
                'logo'  => 'https://cdn-icons-png.flaticon.com/512/124/124010.png',
            ], [
                'title' => 'facebook',
                'logo'  => 'https://cdn-icons-png.flaticon.com/512/124/124010.png',
            ], [
                'title' => 'facebook',
                'logo'  => 'https://cdn-icons-png.flaticon.com/512/124/124010.png',
            ], [
                'title' => 'facebook',
                'logo'  => 'https://cdn-icons-png.flaticon.com/512/124/124010.png',
            ], [
                'title' => 'facebook',
                'logo'  => 'https://cdn-icons-png.flaticon.com/512/124/124010.png',
            ],
        ];

        foreach ($social_connect_list as $key => $single_social_conect) {
            SocialConnect::updateOrCreate(
                ['title' =>  $single_social_conect['title']],
                ['logo' => $single_social_conect['logo']],
            );
        }
    }
}
