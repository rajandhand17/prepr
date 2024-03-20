<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'name'        => 'English',
                'iso'         => 'en',
                'status'      => '1',
                'is_imported' => '1',
            ],
            [
                'name'        => 'French',
                'iso'         => 'fr-CA',
                'status'      => '1',
                'is_imported' => '1',
            ],
        ];

        foreach ($languages as $language) {
            Language::updateOrCreate(['iso' => $language['iso']], $language);
        }
    }
}
