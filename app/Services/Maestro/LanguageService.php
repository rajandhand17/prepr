<?php

namespace App\Services\Maestro;

use App\Models\Language;
use Session;

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

    public static function getAllActiveLanguages()
    {
        try {
            $language = Language::where('status', 1)->get();
            if ($language != null) {
                return $language;
            }

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function getCurrentLanguage()
    {
        try {
            $language = Session::get('globalLocale') ? Session::get('globalLocale') : 'en';
            if ($language != null) {
                return $language;
            }

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }
}
