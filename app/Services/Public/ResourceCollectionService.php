<?php

namespace App\Services\Public;

use App\Models\Duration;
use App\Models\Levels;
use App\Models\ResourceCollection;
use App\Models\ResourceCollectionSocialActivity;

class ResourceCollectionService
{
    public function getResourceCollectionList($request)
    {
        try {
            $resourceCollectionList = ResourceCollection::select();
            $resourceCollectionList = self::filterResourceCollectionList($resourceCollectionList, $request);

            return $resourceCollectionList->paginate(config('site-settings.pagination_per_page'));
        } catch (\Exception $e) {
            return false;
        }
    }

    public function filterResourceCollectionList($resourceCollectionList, $request)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $resourceCollectionList = $resourceCollectionList->where('resource_collections.title', 'like', '%'.$request->search.'%');
            }

            if ($request->has('status') && !empty($request->status)) {
                $status = ($request->status == 'draft') ? '0' : (($request->status == 'published') ? '1' : (($request->status == 'deactivated') ? '2' : '3'));
                $resourceCollectionList = $resourceCollectionList->where('resource_collections.status', $status);
            } else {
                $resourceCollectionList = $resourceCollectionList->where('resource_collections.status', '1');
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
                switch ($request->privacy){
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
            if ($request->has('level') && !empty($request->level)) {
                $level = Levels::where('levels.title', 'like', '%'.$request->level.'%')->pluck('id');
                if ($level) {
                    $resourceCollectionList = $resourceCollectionList->whereIn('resource_collections.level', $level);
                }
            }
            if ($request->has('duration') && $request->duration) {
                $duration = Duration::whereIn('durations.title', 'like', '%'.$request->duration.'%')->pluck('id');
                if ($duration) {
                    $resourceCollectionList = $resourceCollectionList->whereIn('resource_collections.duration', $duration);
                }
            }
            return $resourceCollectionList;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getResourceCollectionBasedOnSlug($slug)
    {
        try {
            return ResourceCollection::where('slug', $slug)->first();
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getResourceCollectionBasedOnId($id)
    {
        try {
            return ResourceCollection::where('id', $id)->select('title', 'uuid', 'media', 'description')->first();
        } catch (\Exception $e) {
            return false;
        }
    }
}
