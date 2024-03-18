<?php

namespace App\Services\Public;

use App\Models\FeaturedModule;

class FeaturedModuleService
{
    public static function getFeaturedLabs()
    {
        try {
            $featuredLabList = FeaturedModule::where('module_type', '0')->take(6)->get();
            return $featuredLabList;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function deleteFeaturedLab($id){
        try {
            $deleteFeaturedModule = FeaturedModule::where([
                ['module_type', '=', '0'],
                ['module_id', '=', $id]
            ])->delete();
            return $deleteFeaturedModule;
        }catch (\Exception $e) {
            return false;
        }
    }
}
