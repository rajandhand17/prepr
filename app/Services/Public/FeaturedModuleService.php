<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\ExplorePageData;
use App\Models\FeaturedModule;

class FeaturedModuleService
{
    public static function getFeaturedLabs()
    {
        try {
            $roles = auth()->user()->roles;
            $role = $roles->pluck('name')->first(); // Get the first role
            $featuredModules = FeaturedModule::where('role', $role)->get();
            return $featuredModules;
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
