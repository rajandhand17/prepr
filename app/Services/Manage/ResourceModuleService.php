<?php

namespace App\Services\Manage;

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
                $resourceModule = $resourceModule->where('resource_module.title', 'like', '%'.$request->search.'%');
            }

            return $resourceModule;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getResourceModuleBasedOnSlug($slug)
    {
        try {
            return resourceModule::where('slug', $slug)->first();
        } catch(\Exception $e) {
            return false;
        }
    }

    public function checkSlug($slug)
    {
        try {
            return resourceModule::where('slug', $slug)->first();
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function delete($slug)
    {
        try {
            return resourceModule::where('slug', $slug)->delete();
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function checkName($title)
    {
        try {
            return resourceModule::where('title', $title)->first();
        } catch(\Exception $e) {
            return false;
        }
    }
}
