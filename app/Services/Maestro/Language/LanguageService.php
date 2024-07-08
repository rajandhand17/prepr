<?php

namespace App\Services\Maestro\Language;


use App\Helpers\UtilityHelper;
use App\Models\Lab;
use App\Models\Language;
use App\Services\Manage\LabMarketplaceService;
use HiFolks\RandoPhp\Randomize;

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
        }catch(\Exception $e){
            return false;
        }
    }
}
