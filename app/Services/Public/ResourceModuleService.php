<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\ResourceModule;
use App\Services\Public\ResourceModuleTypeModesService;

class ResourceModuleService
{
    public static function getResourceModuleList($request)
    {
        try {
            $resourceModule = ResourceModule::where('is_accessible', '1');
            $resourceModule = self::filterResourceModuleList($request, $resourceModule);

            return $resourceModule->paginate(config('site-settings.pagination_per_page'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function filterResourceModuleList($request, $resourceModule)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $resourceModule = $resourceModule->whereSearchFilter($request->search ?? '');
            }
            if ($request->has('organization_id') && !empty($request->organization_id)) {
                $getOrganizationIds = OrganizationService::getOrganizationExistBasedOnUuidArray($request->organization_id)->pluck('id');
                $resourceModule = $resourceModule->whereIn('organization_id', $getOrganizationIds);
            }
            if ($request->filled('social_type') && in_array($request->social_type, ['liked', 'favourites'])) {
                $activityType = ($request->social_type == 'liked') ? 'like' : 'favourite';
                $resourceModuleIds = ResourceModuleSocialActivitiesService::getResourceModuleBasedOnActivity($activityType)->pluck('resource_module_id');
                $resourceModule->whereIn('resource_modules.id', $resourceModuleIds);
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

            if ($request->has('skills') && !empty($request->skills) && is_array($request->skills)) {
                $resourceModule = $resourceModule->whereIn('resource_modules.id', function ($query) use ($request) {
                    $query->select('resource_module_skills_groups_stacks.resource_module_id')
                        ->from('resource_module_skills_groups_stacks')
                        ->whereIn('resource_module_skills_groups_stacks.foreign_id', $request->skills)
                        ->where('resource_module_skills_groups_stacks.type', '0')
                        ->whereNull('resource_module_skills_groups_stacks.deleted_at')
                        ->distinct();
                })->distinct('resource_modules.uuid');
            }
            if ($request->has('tags') && !empty($request->tags) && is_array($request->tags)) {
                $resourceModule = $resourceModule->whereIn('resource_modules.id', function ($query) use ($request) {
                    $query->select('resource_module_tags_groups.resource_module_id')
                        ->from('resource_module_tags_groups')
                        ->whereIn('resource_module_tags_groups.foreign_id', $request->tags)
                        ->where('resource_module_tags_groups.type', '0')
                        ->whereNull('resource_module_tags_groups.deleted_at')
                        ->distinct();
                })->distinct('resource_modules.uuid');
            }
            if ($request->has('duration_id') && $request->duration_id && is_array($request->duration_id)) {
                $resourceModule = $resourceModule->whereIn('duration_id', $request->duration_id);
            }
            if ($request->has('level_id') && $request->level_id && is_array($request->level_id)) {
                $resourceModule = $resourceModule->whereIn('level_id', $request->level_id);
            }

            if ($request->has('rating') && !empty($request->rating)) {
                $resourceModuleRating = ResourceModuleRatingService::getResourceModuleBasedOnRating($request->rating);
                 $resourceModule = $resourceModule->whereIn('id', $resourceModuleRating->pluck('resource_module_id'));

            }
            if ($request->has('type') && $request->type !== null) {
                $resourceModuleType = ResourceModuleTypeModesService::getResourceModuleBasedOnType($request->type);
                $resourceModule = $resourceModule->whereIn('id', $resourceModuleType->pluck('resource_module_id'));
            }
            return $resourceModule;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceModuleBasedOnSlug($slug)
    {
        try {
            return ResourceModule::select()->where('slug', $slug)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceModuleBasedOnId($id)
    {
        try {
            return ResourceModule::select('id', 'uuid', 'title', 'media', 'slug', 'description')->where(['id' => $id, 'is_accessible' => '1'])->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceModuleBasedOnIds($ids)
    {
        try {
            return ResourceModule::whereIn('id', $ids)->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
