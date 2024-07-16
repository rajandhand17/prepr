<?php

namespace App\Services\Maestro;

use App\Models\Language;

class LanguageService
{
    public static function getLanguages()
    {
        try {
            $language = Language::where('status', 1)->pluck('name', 'iso');
            if ($language != null) {
                return $language;
            }

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }
}
