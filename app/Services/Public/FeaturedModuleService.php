<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\FeaturedModule;

class FeaturedModuleService
{
    public static function getFeaturedLabs()
    {
        try {
            $featuredLabList = FeaturedModule::where('module_type', '0')->take(config('site-settings.explore_page_limit_min'))->pluck('module_id');
            $getLabs = LabService::getLabsBasedOnIds($featuredLabList);

            return $getLabs;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteFeaturedLab($id)
    {
        try {
            $deleteFeaturedModule = FeaturedModule::where([
                ['module_type', '=', '0'],
                ['module_id', '=', $id],
            ])->delete();

            return $deleteFeaturedModule;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
