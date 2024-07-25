<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\ResourceGroup;
use App\Models\ResourceGroupRating;
use App\Services\ModuleCompletionStatusService;

class ResourceGroupService
{
    public static function getResourceGroupList($request)
    {
        try {
            $resourceGroupList = ResourceGroup::where('is_accessible', '1');
            $resourceGroupList = self::filterResourceGroupList($resourceGroupList, $request);

            return $resourceGroupList->paginate(config('site-settings.pagination_per_page'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function filterResourceGroupList($resourceGroupList, $request)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $resourceGroupList = $resourceGroupList->whereSearchFilter($request->search ?? '');
            }

            if ($request->has('status') && !empty($request->status)) {
                $status = ($request->status == 'draft') ? '0' : (($request->status == 'published') ? '1' : (($request->status == 'archive') ? '2' : '3'));
                $resourceGroupList = $resourceGroupList->where('resource_groups.status', $status);
            } else {
                $resourceGroupList = $resourceGroupList->where('resource_groups.status', '1');
            }

            if ($request->has('organization_id') && !empty($request->organization_id)) {
                $getOrganizationIds = OrganizationService::getOrganizationExistBasedOnUuidArray($request->organization_id)->pluck('id');
                $resourceGroupList = $resourceGroupList->whereIn('organization_id', $getOrganizationIds);
            }

            if ($request->filled('social_type') && in_array($request->social_type, ['liked', 'favourites'])) {
                $activityType = ($request->social_type == 'liked') ? 'like' : 'favourite';
                $resourceIds = ResourceGroupSocialActivitiesService::getResourceGroupsBasedOnActivity($activityType)->pluck('resource_group_id');
                $resourceGroupList->whereIn('resource_groups.id', $resourceIds);
            }

            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $resourceGroupList = $resourceGroupList->orderBy('resource_groups.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $resourceGroupList = $resourceGroupList->orderBy('resource_groups.title', 'DESC');
                        break;
                    case 'creation_date':
                        $resourceGroupList = $resourceGroupList->orderBy('resource_groups.created_at', 'ASC');
                        break;
                    default:
                        $resourceGroupList = $resourceGroupList->orderBy('resource_groups.id', 'ASC');
                }
            }

            if ($request->has('privacy')) {
                $privacy = null;
                switch ($request->privacy) {
                    case 'yes':
                        $privacy = config('constants.resource_group_privacy.yes');
                        break;
                    case 'no':
                        $privacy = config('constants.resource_group_privacy.no');
                        break;
                    default:
                        $privacy = null;
                }
                if ($privacy != null) {
                    $resourceGroupList = $resourceGroupList->where('privacy', $privacy);
                }
            }

            if ($request->has('skills') && !empty($request->skills) && is_array($request->skills)) {
                $resourceGroupList = $resourceGroupList->whereIn('resource_groups.id', function ($query) use ($request) {
                    $query->select('resource_groups_skills_groups_stacks.resource_group_id')
                        ->from('resource_groups_skills_groups_stacks')
                        ->whereIn('resource_groups_skills_groups_stacks.foreign_id', $request->skills)
                        ->where('resource_groups_skills_groups_stacks.type', '0')
                        ->whereNull('resource_groups_skills_groups_stacks.deleted_at')
                        ->distinct();
                })->distinct('resource_groups.uuid');
            }
            if ($request->has('tags') && !empty($request->tags) && is_array($request->tags)) {
                $resourceGroupList = $resourceGroupList->whereIn('resource_groups.id', function ($query) use ($request) {
                    $query->select('resource_group_tags_groups.challenge_id')
                        ->from('resource_group_tags_groups')
                        ->whereIn('resource_group_tags_groups.foreign_id', $request->tags)
                        ->where('resource_group_tags_groups.type', '0')
                        ->whereNull('resource_group_tags_groups.deleted_at')
                        ->distinct();
                })->distinct('resource_groups.uuid');
            }
            if ($request->has('level_id') && $request->level_id && is_array($request->level_id)) {
                $resourceGroupList = $resourceGroupList->whereIn('level', $request->level_id);
            }
            if ($request->has('duration_id') && $request->duration_id && is_array($request->duration_id)) {
                $resourceGroupList = $resourceGroupList->whereIn('duration', $request->duration_id);
            }
            if ($request->has('rating') && !empty($request->rating)) {
                $getResourceGroupList = ResourceGroupRatingService::getResourceGroupBasedOnRating($request->rating);
                $resourceGroupList = $resourceGroupList->whereIn('id', $getResourceGroupList->pluck('resource_group_id'));
            }

            if ($request->has('type') && $request->type !== null) {
                $resourceGroupType = ResourceGroupTypeModesService::getResourceGroupBasedOnType($request->type);
                $resourceGroupList = $resourceGroupList->whereIn('id', $resourceGroupType->pluck('resource_group_id'));
            }
            if ($request->has('progress') && !empty($request->progress)) {
                $resourceGroupProgress = [];
                $moduleType = config('constants.module_completion_statuses_types.resource_group');
                switch ($request->progress) {
                    case 'not-started':
                        $resourceGroupProgress = ModuleCompletionStatusService::getResourceProgress($moduleType, config('constants.status_module_completion.not_started'));
                        break;
                    case 'in-progress':
                        $resourceGroupProgress = ModuleCompletionStatusService::getResourceProgress($moduleType, config('constants.status_module_completion.in_progress'));
                        break;
                    case 'complete':
                        $resourceGroupProgress = ModuleCompletionStatusService::getResourceProgress($moduleType, config('constants.status_module_completion.completed'));
                        break;
                }
                $resourceGroupList = $resourceGroupList->whereIn('id', $resourceGroupProgress->pluck('module_id'));
            }

            return $resourceGroupList;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceGroupBasedOnSlug($slug)
    {
        try {
            return ResourceGroup::where('slug', $slug)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function addRating($resource_group_id, $request)
    {
        try {
            ResourceGroupRating::updateOrInsert([
                'resource_group_id'     => $resource_group_id,
                'user_id'               => auth()->user()->id,
            ], [
                'rating' => $request->rating,
            ]);

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceGroupBasedOnId($id)
    {
        try {
            return ResourceGroup::where(['id' => $id, 'is_accessible' => '1'])->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceGroupBasedOnArrayIds($resourceGroupIds)
    {
        try {
            $resourceGroupList = ResourceGroup::whereIn('id', $resourceGroupIds)->where('is_accessible', '1')->get();

            return $resourceGroupList;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
