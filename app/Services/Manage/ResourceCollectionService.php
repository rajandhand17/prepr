<?php

namespace App\Services\Manage;

use App\Events\ResourceCollection\DeleteResourceCollectionAssociatedData;
use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Duration;
use App\Models\Levels;
use App\Models\ResourceCollection;
use App\Models\ResourceModule;
use HiFolks\RandoPhp\Randomize;

class ResourceCollectionService
{
    public static function createResourceCollection($request, $upload_cover_image)
    {
        try {
            $status = config('constants.resource_collection_status.draft');
            switch($request->status) {
                case 'published':
                    $status = config('constants.resource_collection_status.publish');
                    break;
                case 'archive':
                    $status = config('constants.resource_collection_status.archive');
                    break;
                default:
                    $status = config('constants.resource_collection_status.draft');
                    break;
            }

            switch ($request->privacy) {
                case 'no':
                    $privacy = config('constants.resource_collection_privacy.no');
                    break;
                case 'yes':
                    $privacy = config('constants.resource_collection_privacy.yes');
                    break;
                default:
                    $privacy = null;
            }
            switch ($request->is_accessible) {
                case 'no':
                    $is_accessible = config('constants.resource_collection_is_accessible.no');
                    break;
                case 'yes':
                    $is_accessible = config('constants.resource_collection_is_accessible.yes');
                    break;
                default:
                    $is_accessible = config('constants.resource_collection_is_accessible.no');
            }
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            $resourceCollection = new ResourceCollection();
            $slug = UtilityHelper::generateSlug($request->title, $resourceCollection);
            $resourceCollection->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $resourceCollection->language = $request->language;
            $resourceCollection->user_id = auth()->user()->id;
            $resourceCollection->organization_id = $organization->id;
            $resourceCollection->title = $request->title;
            $resourceCollection->slug = $slug;
            $resourceCollection->description = $request->description;
            $resourceCollection->media_type = $request->media_type;
            $resourceCollection->media = $upload_cover_image;
            $resourceCollection->level = $request->level;
            $resourceCollection->duration = $request->duration;
            $resourceCollection->privacy = $privacy;
            $resourceCollection->status = $status;
            $resourceCollection->is_accessible = $is_accessible;
            $resourceCollection->save();

            return $resourceCollection;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function uploadResourceCollectionCoverImage($cover_image)
    {
        try {
            $upload_resource_collection_cover_image = FileUploadHelper::uploadImageToS3($cover_image, 'resource_collection');
            if ($upload_resource_collection_cover_image == false) {
                return false;
            }

            return $upload_resource_collection_cover_image;
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

    public static function checkName($title)
    {
        try {
            return ResourceCollection::select('id')->where('title', $title)->first();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateResourceCollection($slug, $request, $upload_cover_image)
    {
        try {
            $resourceCollection = ResourceCollection::where('slug', $slug)->first();
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            if ($resourceCollection !== null) {
                $status = $resourceCollection->status;
                $privacy = $resourceCollection->privacy;
                $is_accessible = $resourceCollection->is_accessible;
                switch($request->status) {
                    case 'published':
                        $status = config('constants.resource_collection_status.publish');
                        break;
                    case 'archive':
                        $status = config('constants.resource_collection_status.archive');
                        break;
                    default:
                        $status = config('constants.resource_collection_status.draft');
                        break;
                }

                switch ($request->privacy) {
                    case 'no':
                        $privacy = config('constants.resource_collection_privacy.no');
                        break;
                    case 'yes':
                        $privacy = config('constants.resource_collection_privacy.yes');
                        break;
                    default:
                        $privacy = null;
                }
                switch ($request->is_accessible) {
                    case 'no':
                        $is_accessible = config('constants.resource_collection_is_accessible.no');
                        break;
                    case 'yes':
                        $is_accessible = config('constants.resource_collection_is_accessible.yes');
                        break;
                    default:
                        $is_accessible = config('constants.resource_collection_is_accessible.no');
                }
                $resourceCollection->language = ($request->has('language')) ? $request->language : $resourceCollection->language;
                $resourceCollection->organization_id = $organization->id;
                $resourceCollection->title = ($request->has('title')) ? $request->title : $resourceCollection->title;
                $resourceCollection->description = ($request->has('description')) ? $request->description : $resourceCollection->description;
                $resourceCollection->media = ($upload_cover_image != null) ? $upload_cover_image : $resourceCollection->cover_image;
                $resourceCollection->level = ($request->has('level')) ? $request->level : $resourceCollection->level;
                $resourceCollection->duration = ($request->has('duration')) ? $request->duration : $resourceCollection->duration;
                $resourceCollection->privacy = $privacy;
                $resourceCollection->status = $status;
                $resourceCollection->is_accessible = $is_accessible;
                $resourceCollection->save();

                return $resourceCollection;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getResourceCollectionList($request, $organization)
    {
        try {
            $resourceCollectionList = ResourceCollection::select()->where('organization_id', '=', $organization->id);
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

    public static function deleteResourceCollection($resource_collection_id)
    {
        try {
            $resourceCollection = ResourceCollection::find($resource_collection_id)->delete();
            if ($resourceCollection) {
                event(new DeleteResourceCollectionAssociatedData($resource_collection_id));

                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getResourceCollectionBasedOnUUIDArray($resourceCollectionUUIDArray)
    {
        try {
            $resourceCollectionIds = ResourceCollection::whereIn('uuid', $resourceCollectionUUIDArray)->pluck('id')->all();
            if ($resourceCollectionIds != null) {
                return $resourceCollectionIds;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getResourceCollectionBasedOnId($id)
    {
        try {
            return ResourceCollection::whereIN('id', $id)->pluck('title','uuid');
        } catch (\Exception $e) {
            return false;
        }
    }
}
