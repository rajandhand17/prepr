<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\Duration;
use App\Models\Levels;
use App\Models\ResourceGroup;
use App\Models\ResourceGroupRating;

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
                $resourceGroupList = $resourceGroupList->where('resource_groups.title', 'like', '%'.$request->search.'%');
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
            if ($request->has('level') && !empty($request->level)) {
                $level = Levels::where('levels.title', 'like', '%'.$request->level.'%')->pluck('id');
                if ($level) {
                    $resourceGroupList = $resourceGroupList->whereIn('resource_groups.level', $level);
                }
            }
            if ($request->has('duration') && $request->duration) {
                $duration = Duration::where('durations.title', 'like', '%'.$request->duration.'%')->pluck('id');
                if ($duration) {
                    $resourceGroupList = $resourceGroupList->whereIn('resource_groups.duration', $duration);
                }
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
}
