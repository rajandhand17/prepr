<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\ResourceModule;
use App\Services\Manage\ResourceModuleSkillsGroupsStackService;
use App\Services\ModuleCompletionStatusService;

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
            if ($request->has('progress') && !empty($request->progress)) {
                $resourceModulesProgress = [];
                $moduleType = config('constants.module_completion_statuses_types.resource_module');
                switch ($request->progress) {
                    case 'not-started':
                        $resourceModulesProgress = ModuleCompletionStatusService::getResourceProgress($moduleType, config('constants.status_module_completion.not_started'));
                        break;
                    case 'in-progress':
                        $resourceModulesProgress = ModuleCompletionStatusService::getResourceProgress($moduleType, config('constants.status_module_completion.in_progress'));
                        break;
                    case 'complete':
                        $resourceModulesProgress = ModuleCompletionStatusService::getResourceProgress($moduleType, config('constants.status_module_completion.completed'));
                        break;
                }
                if (!empty($resourceModulesProgress)) {
                    $resourceModule = $resourceModule->whereIn('id', $resourceModulesProgress->pluck('module_id'));
                }
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

    public static function getAll()
    {
        try {
            return ResourceModule::select();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchRecommendedResourceModules($fetchUserSkills, $userData)
    {
        try {
            $getResourceModulesIdsBasedOnSKills = ResourceModuleSkillsGroupsStackService::getResourceModuleIdBasesOnSKillsId($fetchUserSkills);
            $resourceModuleIds = $getResourceModulesIdsBasedOnSKills->unique();
            $fetchRecommendedResourceModules = ResourceModule::whereIn('id', $resourceModuleIds)->where('user_id', '!=', $userData->id)->take(config('site-settings.dashboard_page_limit_max'))->get();

            return $fetchRecommendedResourceModules;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function myResourceModuleIds($userData)
    {
        try {
            $fetchResourceModuleIdsBasedOnProgress = ModuleCompletionStatusService::fetchResourceModuleIdsBasedOnProgress($userData);
            $myResourceModuleIds = ResourceModule::whereIn('id', $fetchResourceModuleIdsBasedOnProgress)->pluck('id');
            if (!empty($myResourceModuleIds)) {
                return $myResourceModuleIds;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceModuleDashboardList($resourceModuleIds)
    {
        try {
            $resourceModule = ResourceModule::whereIn('id', $resourceModuleIds)->where('is_accessible', '1');

            return $resourceModule->paginate(config('site-settings.pagination_per_page'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchMyResourceModuleProgress($userData)
    {
        try {
            $inProgressResourceModulesCount = 0;
            $completedResourceModulesCount = 0;
            $fetchResourceModuleBasedOnUserId = ModuleCompletionStatusService::fetchResourceModuleBasedOnUserId($userData);
            if ($fetchResourceModuleBasedOnUserId->isNotEmpty()) {
                foreach ($fetchResourceModuleBasedOnUserId as $fetchResourceModule) {
                    if ($fetchResourceModule->percentage == '100') {
                        $completedResourceModulesCount++;
                    } elseif ($fetchResourceModule->percentage > '0' && $fetchResourceModule->percentage < '100') {
                        $inProgressResourceModulesCount++;
                    }
                }
            }
            $overAllJoinedResourceModules = ($inProgressResourceModulesCount + $completedResourceModulesCount);

            $fetchMyResourceModuleProgress = ['overAllJoined' => $overAllJoinedResourceModules, 'completedCount' => $completedResourceModulesCount, 'inProgressCount' => $inProgressResourceModulesCount];

            return $fetchMyResourceModuleProgress;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getComponentBasedResourceModuleList($request, $organizationId)
    {
        try {
            $resourceModule = ResourceModule::where(['organization_id' => $organizationId, 'status' => '1', 'is_accessible' => '1']);
            $resourceModule = self::filterResourceModuleList($request, $resourceModule);

            return $resourceModule->paginate(config('site-settings.association_pagination_per_page'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchResourceModuleAssociation($request, $fetchResourceModuleAssociation)
    {
        try {
            $resourceModule = ResourceModule::whereIn('id', $fetchResourceModuleAssociation)->where(['status' => '1', 'is_accessible' => '1']);
            $resourceModule = self::filterResourceModuleList($request, $resourceModule);

            return $resourceModule->paginate(config('site-settings.association_pagination_per_page'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getRelatedResourceModules($resourceModuleIds)
    {
        try {
            // Retrieve resource module with the given IDs using findMany for primary keys and limiting by 2 values
            $resourceModules = ResourceModule::findMany($resourceModuleIds)->slice(0, 2);

            return $resourceModules;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
