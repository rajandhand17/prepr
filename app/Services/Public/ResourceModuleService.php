<?php

namespace App\Services\Public;

use App\Models\ResourceModule;

class ResourceModuleService
{
    public static function getResourceModuleList($request)
    {
        try {
            $resourceModule = ResourceModule::select();
            $resourceModule = self::filterResourceModuleList($request, $resourceModule);

            return $resourceModule->paginate(config('site-settings.pagination_per_page'));
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function filterResourceModuleList($request, $resourceModule)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $resourceModule = $resourceModule->where('resource_modules.title', 'like', '%'.$request->search.'%');
            }

            return $resourceModule;
        } catch(\Exception $e) {
            return false;
        }
    }
}
