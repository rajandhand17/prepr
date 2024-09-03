<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\ResourceCollection;
use App\Services\ModuleCompletionStatusService;

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
                    case 'private':
                        $privacy = config('constants.resource_collection_privacy.yes');
                        break;
                    case 'public':
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
                $resourceCollectionList = $resourceCollectionList->whereIn('id', $getResourceCollectionsRating->pluck('resource_collection_id'));
            }

            if ($request->has('type') && $request->type !== null) {
                $resourceCollectionType = ResourceCollectionTypeModesService::getResourceCollectionBasedOnType($request->type);
                $resourceCollectionList = $resourceCollectionList->whereIn('id', $resourceCollectionType->pluck('resource_collection_id'));
            }
            if ($request->has('progress') && !empty($request->progress)) {
                $resourceCollectionProgress = [];
                $moduleType = config('constants.module_completion_statuses_types.resource_collection');
                switch ($request->progress) {
                    case 'not-started':
                        $resourceCollectionProgress = ModuleCompletionStatusService::getResourceProgress($moduleType, config('constants.status_module_completion.not_started'));
                        break;
                    case 'in-progress':
                        $resourceCollectionProgress = ModuleCompletionStatusService::getResourceProgress($moduleType, config('constants.status_module_completion.in_progress'));
                        break;
                    case 'complete':
                        $resourceCollectionProgress = ModuleCompletionStatusService::getResourceProgress($moduleType, config('constants.status_module_completion.completed'));
                        break;
                }
                if (!empty($resourceCollectionProgress)) {
                    $resourceCollectionList = $resourceCollectionList->whereIn('id', $resourceCollectionProgress->pluck('module_id'));
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

    public function getComponentBasedResourceCollectionList($request, $organizationId)
    {
        try {
            $resourceCollectionList = ResourceCollection::where(['organization_id' => $organizationId, 'status' => '1', 'is_accessible' => '1']);
            $resourceCollectionList = self::filterResourceCollectionList($resourceCollectionList, $request);

            return $resourceCollectionList->paginate(config('site-settings.association_pagination_per_page'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchResourceCollectionAssociation($request, $fetchResourceCollectionAssociation)
    {
        try {
            $resourceCollectionList = ResourceCollection::whereIn('id', $fetchResourceCollectionAssociation)->where(['status' => '1', 'is_accessible' => '1']);
            $resourceCollectionList = self::filterResourceCollectionList($resourceCollectionList, $request);

            return $resourceCollectionList->paginate(config('site-settings.association_pagination_per_page'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getRelatedResourceCollections($resourceCollectionIds)
    {
        try {
            // Retrieve resource collection with the given IDs using findMany for primary keys and limiting by 2 values
            $resourceCollections = ResourceCollection::findMany($resourceCollectionIds)->slice(0, 2);

            return $resourceCollections;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
