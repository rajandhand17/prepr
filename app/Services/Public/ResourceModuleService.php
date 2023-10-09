<?php

namespace App\Services\Public;

use App\Models\ResourceModule;
use App\Models\ResourceModuleRating;

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

            if ($request->has('status') && !empty($request->status)) {
                switch ($request->status) {
                    case 'draft':
                        $status = config('constants.resource_module_status.draft');
                        break;
                    case 'published':
                        $status = config('constants.resource_module_status.publish');
                        break;
                    case 'archive':
                        $status = config('constants.resource_module_status.archive');
                        break;
                    default:
                        $status = null;
                }
                $resourceModule = $resourceModule->where('resource_modules.status', $status);
            }

            if ($request->has('privacy') && !empty($request->privacy)) {
                switch ($request->privacy) {
                    case 'no':
                        $privacy = config('constants.resource_module_privacy.no');
                        break;
                    case 'yes':
                        $privacy = config('constants.resource_module_privacy.yes');
                        break;
                    default:
                        $privacy = null;
                }
                $resourceModule = $resourceModule->where('resource_modules.privacy', $privacy);
            }
            if ($request->has('is_global') && !empty($request->is_global)) {
                switch ($request->is_global) {
                    case 'no':
                        $is_global = config('constants.resource_module_is_global.no');
                        break;
                    case 'yes':
                        $is_global = config('constants.resource_module_is_global.yes');
                        break;
                    default:
                        $privacy = null;
                }

                $resourceModule = $resourceModule->where('resource_modules.is_global', $is_global);
            }
            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $resourceModule = $resourceModule->orderBy('resource_modules.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $resourceModule = $resourceModule->orderBy('resource_modules.title', 'DESC');
                        break;
                    case 'creation_date':
                        $resourceModule = $resourceModule->orderBy('resource_modules.created_at', 'ASC');
                        break;
                    default:
                        $resourceModule = $resourceModule->orderBy('resource_modules.id', 'ASC');
                }
            }

            return $resourceModule;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function getResourceModuleBasedOnSlug($slug)
    {
        try {
            return ResourceModule::select()->where('slug', $slug)->first();
        } catch(\Exception $e) {
            return false;
        }
    }

    public function checkSlug($slug)
    {
        try {
            return ResourceModule::where('slug', $slug)->first();
        } catch(\Exception $e) {
            return false;
        }
    }

    public function checkRating($resource_module_id, $request)
    {
        try {
            $checkReview = ResourceModuleRating::where(
                [
                    'resource_module_id'=> $resource_module_id,
                    'user_id'           => auth()->user()->id,
                ]
            )->first();
            if ($checkReview != null) {
                return true;
            }
            return false;
        } catch(\Exception $e) {
            return false;
        }
    }
}
