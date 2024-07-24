<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\ResourceCollection;

class ResourceCollectionService
{
    public function getResourceCollectionList($request)
    {
        try {
            $resourceCollectionList = ResourceCollection::where('is_accessible', '1');
            $resourceCollectionList = self::filterResourceCollectionList($resourceCollectionList, $request);

            return $resourceCollectionList->paginate(config('site-settings.pagination_per_page'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function filterResourceCollectionList($resourceCollectionList, $request)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $resourceCollectionList = $resourceCollectionList->whereSearchFilter($request->search ?? '');
            }
            if ($request->has('organization_id') && !empty($request->organization_id)) {
                $getOrganizationIds = OrganizationService::getOrganizationExistBasedOnUuidArray($request->organization_id)->pluck('id');
                $resourceCollectionList = $resourceCollectionList->whereIn('organization_id', $getOrganizationIds);
            }

            if ($request->has('status') && !empty($request->status)) {
                $status = ($request->status == 'draft') ? '0' : (($request->status == 'published') ? '1' : (($request->status == 'archive') ? '2' : '3'));
                $resourceCollectionList = $resourceCollectionList->where('resource_collections.status', $status);
            }

            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $resourceCollectionList = $resourceCollectionList->orderBy('resource_collections.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $resourceCollectionList = $resourceCollectionList->orderBy('resource_collections.title', 'DESC');
                        break;
                    case 'creation_date':
                        $resourceCollectionList = $resourceCollectionList->orderBy('resource_collections.created_at', 'ASC');
                        break;
                    default:
                        $resourceCollectionList = $resourceCollectionList->orderBy('resource_collections.id', 'ASC');
                }
            }

            if ($request->has('privacy')) {
                $privacy = null;
                switch ($request->privacy) {
                    case 'yes':
                        $privacy = config('constants.resource_collection_privacy.yes');
                        break;
                    case 'no':
                        $privacy = config('constants.resource_collection_privacy.no');
                        break;
                    default:
                        $privacy = null;
                }
                if ($privacy != null) {
                    $resourceCollectionList = $resourceCollectionList->where('privacy', $privacy);
                }
            }
            if ($request->has('skills') && !empty($request->skills) && is_array($request->skills)) {
                $resourceCollectionList = $resourceCollectionList->whereIn('resource_collections.id', function ($query) use ($request) {
                    $query->select('resource_collection_skills_groups_stacks.resource_collection_id')
                        ->from('resource_collection_skills_groups_stacks')
                        ->whereIn('resource_collection_skills_groups_stacks.foreign_id', $request->skills)
                        ->where('resource_collection_skills_groups_stacks.type', '0')
                        ->whereNull('resource_collection_skills_groups_stacks.deleted_at')
                        ->distinct();
                })->distinct('resource_collections.uuid');
            }
            if ($request->has('tags') && !empty($request->tags) && is_array($request->tags)) {
                $resourceCollectionList = $resourceCollectionList->whereIn('resource_collections.id', function ($query) use ($request) {
                    $query->select('resource_collection_tags_groups.challenge_id')
                        ->from('resource_collection_tags_groups')
                        ->whereIn('resource_collection_tags_groups.foreign_id', $request->tags)
                        ->where('resource_collection_tags_groups.type', '0')
                        ->whereNull('resource_collection_tags_groups.deleted_at')
                        ->distinct();
                })->distinct('resource_collections.uuid');
            }
            if ($request->has('level_id') && $request->level_id && is_array($request->level_id)) {
                $resourceCollectionList = $resourceCollectionList->whereIn('level', $request->level_id);
            }
            if ($request->has('duration_id') && $request->duration_id && is_array($request->duration_id)) {
                $resourceCollectionList = $resourceCollectionList->whereIn('duration', $request->duration_id);
            }

            if ($request->has('social_type') && !empty($request->social_type) && $request->social_type == 'liked') {
                $getCollectionLikedList = ResourceCollectionSocialActivitiesService::getResourceCollectionBasedOnActivity('like');
                $resourceCollectionList = $resourceCollectionList->whereIn('id', $getCollectionLikedList->pluck('resource_collection_id'));
            }
            if ($request->has('social_type') && !empty($request->social_type) && $request->social_type == 'favourites') {
                $getCollectionFavouriteList = ResourceCollectionSocialActivitiesService::getResourceCollectionBasedOnActivity('favourite');
                $resourceCollectionList = $resourceCollectionList->whereIn('id', $getCollectionFavouriteList->pluck('resource_collection_id'));
            }

            if ($request->has('social_type') && !empty($request->social_type) && $request->social_type == 'shared') {
                $getCollectionFavouriteList = ResourceCollectionSocialActivitiesService::getResourceCollectionBasedOnActivity('share');
                $resourceCollectionList = $resourceCollectionList->whereIn('id', $getCollectionFavouriteList->pluck('resource_collection_id'));
            }
            if ($request->has('rating') && !empty($request->rating)) {
                $getResourceCollectionsRating = ResourceCollectionRatingService::getResourceCollectionBasedOnRating($request->rating);
                if (count($getResourceCollectionsRating) > 0) {
                    $resourceCollectionList = $resourceCollectionList->whereIn('id', $getResourceCollectionsRating->pluck('resource_collection_id'));
                }
            }

            return $resourceCollectionList;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceCollectionBasedOnSlug($slug)
    {
        try {
            return ResourceCollection::where('slug', $slug)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceCollectionBasedOnId($id)
    {
        try {
            return ResourceCollection::where(['id' => $id, 'is_accessible' => '1'])->select('title', 'uuid', 'media', 'description', 'slug')->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceCollectionBasedOnArrayIds($ids)
    {
        try {
            return ResourceCollection::whereIn('id', $ids)->where('is_accessible', '1')->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
