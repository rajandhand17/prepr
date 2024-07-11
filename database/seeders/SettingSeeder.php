<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\Skill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //0->BOOLEAN,1->NUMBER,2->DATE,3->TEXT,4->SELECT,5->FILE,6->TEXTAREA
        $settings = [
            [
                'code'        => 'site_name',
                'module_type' => '3',
                'label'       => 'Site name',
                'value'       => 'Prepr Network',
            ],
            [
                'code'        => 'site_logo',
                'module_type' => '5',
                'label'       => 'site logo',
                'value'       => 'site_logo.png',
            ],
            [
                'code'        => 'favicon',
                'module_type' => '5',
                'label'       => 'Favicon',
                'value'       => 'favicon.ico',
            ],
            [
                'code'        => 'site_description',
                'module_type' => '3',
                'label'       => 'Site Description',
                'value'       => 'Prepr connects students and startups to employees and employers.',
            ],
            [
                'code'        => 'site_terms',
                'module_type' => '6',
                'label'       => 'Terms and Conditions',
                'value'       => 'Prepr connects students and startups to employees and employers.',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['code' => $setting['code']], $setting);
        }
    }
}
