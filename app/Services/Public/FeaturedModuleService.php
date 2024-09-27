<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\FeaturedModule;

class FeaturedModuleService
{
    public static function getFeaturedModule()
    {
        try {
            $roles = auth()->user()->roles;
            $role = $roles->pluck('id')->unique();
            $roleArray = $role->toArray();
            $featuredModules = FeaturedModule::where(function ($query) use ($roleArray) {
                foreach ($roleArray as $role) {
                    $query->orWhereRaw('JSON_CONTAINS(role, ?)', [json_encode((string) $role)]);
                }
            })->get();

            return $featuredModules;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteFeaturedModule($moduleType,$id)
    {
        try {
            $deleteFeaturedModule = FeaturedModule::where([
                ['module_type', '=', $moduleType],
                ['module_id', '=', $id],
            ])->delete();

            return $deleteFeaturedModule;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
