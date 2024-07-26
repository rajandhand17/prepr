<?php

namespace App\Services\Manage;

use App\Events\ResourceCollection\DeleteResourceCollectionAssociatedData;
use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\ResourceCollection;
use App\Services\ModuleCompletionStatusService;
use Exception;
use HiFolks\RandoPhp\Randomize;

class ResourceCollectionService
{
    public function getResourceCollectionCountBasedOnOrganization($organizationId)
    {
        try {
            $resourceCollection_count = ResourceCollection::where('organization_id', $organizationId)->count();

            return $resourceCollection_count;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createResourceCollection($request, $upload_cover_image, $organizationId)
    {
        try {
            $status = config('constants.resource_collection_status.draft');
            switch ($request->status) {
                case 'publish':
                    $status = config('constants.resource_collection_status.publish');
                    break;
                case 'archive':
                    $status = config('constants.resource_collection_status.archive');
                    break;
                default:
                    $status = config('constants.resource_collection_status.draft');
                    break;
            }
            $media_type = config('constants.resource_media_type.image');
            switch ($request->media_type) {
                case 'image':
                    $media_type = config('constants.resource_media_type.image');
                    break;
                case 'embedded':
                    $media_type = config('constants.resource_media_type.embedded');
                    break;
                default:
                    $media_type = null;
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
            $resourceCollection = new ResourceCollection();
            $slug = UtilityHelper::generateSlug($request->title, $resourceCollection);
            $resourceCollection->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $resourceCollection->language = $request->language;
            $resourceCollection->user_id = auth()->user()->id;
            $resourceCollection->organization_id = $organizationId;
            $resourceCollection->title = $request->title;
            $resourceCollection->slug = $slug;
            $resourceCollection->description = $request->description;
            $resourceCollection->media_type = $media_type;
            $resourceCollection->media = $upload_cover_image;
            $resourceCollection->level = $request->level;
            $resourceCollection->duration = $request->duration;
            $resourceCollection->privacy = $privacy;
            $resourceCollection->status = $status;
            $resourceCollection->save();

            return $resourceCollection;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

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

    public static function getResourceCollectionBasedOnTitle($title)
    {
        try {
            return ResourceCollection::where(['title'=>$title, 'user_id'=>auth()->user()->id])->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkName($title)
    {
        try {
            return ResourceCollection::select('id')->where('title', $title)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateResourceCollection($slug, $request, $upload_cover_image, $organizationId)
    {
        try {
            $resourceCollection = ResourceCollection::where('slug', $slug)->first();
            if ($resourceCollection !== null) {
                $status = $resourceCollection->status;
                $privacy = $resourceCollection->privacy;
                $is_accessible = $resourceCollection->is_accessible;
                switch ($request->status) {
                    case 'publish':
                        $status = config('constants.resource_collection_status.publish');
                        break;
                    case 'archive':
                        $status = config('constants.resource_collection_status.archive');
                        break;
                    default:
                        $status = config('constants.resource_collection_status.draft');
                        break;
                }
                $media_type = config('constants.resource_media_type.image');
                switch ($request->media_type) {
                    case 'image':
                        $media_type = config('constants.resource_media_type.image');
                        break;
                    case 'embedded':
                        $media_type = config('constants.resource_media_type.embedded');
                        break;
                    default:
                        $media_type = null;
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

                $resourceCollection->language = ($request->has('language')) ? $request->language : $resourceCollection->language;
                $resourceCollection->organization_id = $organizationId;
                $resourceCollection->title = ($request->has('title')) ? $request->title : $resourceCollection->title;
                $resourceCollection->description = ($request->has('description')) ? $request->description : $resourceCollection->description;
                $resourceCollection->media = ($upload_cover_image != null) ? $upload_cover_image : $resourceCollection->cover_image;
                $resourceCollection->media_type = ($request->has('media_type')) ? $media_type : $resourceCollection->media_type;
                $resourceCollection->level = ($request->has('level')) ? $request->level : $resourceCollection->level;
                $resourceCollection->duration = ($request->has('duration')) ? $request->duration : $resourceCollection->duration;
                $resourceCollection->privacy = $privacy;
                $resourceCollection->status = $status;
                $resourceCollection->save();

                return $resourceCollection;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

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

            if ($request->has('status') && !empty($request->status)) {
                $status = ($request->status == 'draft') ? '0' : (($request->status == 'published') ? '1' : (($request->status == 'archive') ? '2' : '3'));
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
            if ($request->has('level_id') && $request->level_id && is_array($request->level_id)) {
                $resourceCollectionList = $resourceCollectionList->whereIn('level', $request->level_id);
            }
            if ($request->has('duration_id') && $request->duration_id && is_array($request->duration_id)) {
                $resourceCollectionList = $resourceCollectionList->whereIn('duration', $request->duration_id);
            }
            if ($request->has('social_type') && !empty($request->social_type) && $request->social_type == 'liked') {
                $getCollectionLikedList = ResourceCollectionSocialActivitiesService::getResourceCollectionBasedOnActivity('like');
                if ($getCollectionLikedList && $getCollectionLikedList->count() > 0) {
                    $resourceCollectionList = $resourceCollectionList->whereIn('id', $getCollectionLikedList->pluck('resource_collection_id'));
                }
            }
            if ($request->has('social_type') && !empty($request->social_type) && $request->social_type == 'favourites') {
                $getCollectionFavouriteList = ResourceCollectionSocialActivitiesService::getResourceCollectionBasedOnActivity('favourite');
                if ($getCollectionFavouriteList && $getCollectionFavouriteList->count() > 0) {
                    $resourceCollectionList = $resourceCollectionList->whereIn('id', $getCollectionFavouriteList->pluck('resource_collection_id'));
                }
            }

            if ($request->has('social_type') && !empty($request->social_type) && $request->social_type == 'shared') {
                $getCollectionFavouriteList = ResourceCollectionSocialActivitiesService::getResourceCollectionBasedOnActivity('share');
                if ($getCollectionFavouriteList && $getCollectionFavouriteList->count() > 0) {
                    $resourceCollectionList = $resourceCollectionList->whereIn('id', $getCollectionFavouriteList->pluck('resource_collection_id'));
                }
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
                $resourceGroupProgress = [];
                $moduleType = config('constants.module_completion_statuses_types.resource_collection');
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
                $resourceCollectionList = $resourceCollectionList->whereIn('id', $resourceGroupProgress->pluck('module_id'));
            }

            return $resourceCollectionList;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceCollectionBasedOnId($id)
    {
        try {
            return ResourceCollection::select('title', 'uuid', 'media', 'description', 'slug')->where(['id' => $id, 'is_accessible' => '1'])->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceCollectionsBasedOnId($id)
    {
        try {
            return ResourceCollection::where(['id' => $id, 'is_accessible' => '1'])->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceCollectionGetBasedId($id)
    {
        try {
            $resourceCollectionIds = ResourceCollection::whereIn('id', $id)->pluck('id')->all();
            if ($resourceCollectionIds != null) {
                return $resourceCollectionIds;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getListName($request, $organization)
    {
        try {
            $resourceCollectionList = ResourceCollection::select('uuid', 'title', 'media')->where(['organization_id' => $organization->id, 'is_accessible' => '1']);
            $resourceCollectionList = self::filterResourceCollectionList($resourceCollectionList, $request);

            return $resourceCollectionList->paginate(config('site-settings.pagination_per_page'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceCollectionBasedOnUUID($uUID)
    {
        try {
            return ResourceCollection::select('id', 'uuid', 'title', 'media', 'slug', 'description')->where('UUID', $uUID)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceCollectionsBasedOnIds($ids)
    {
        try {
            return ResourceCollection::select()->whereIn('id', $ids)->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourcesWithRelations($id)
    {
        try {
            return ResourceCollection::with('component_association', 'skills_groups_stack', 'resource_collection_type_modes')->find($id);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteOrganizationResourceCollection($organizationId)
    {
        try {
            $fetchOrganizationResourceCollections = ResourceCollection::where('organization_id', $organizationId)->pluck('id');
            if (!empty($fetchOrganizationResourceCollections)) {
                foreach ($fetchOrganizationResourceCollections as $organizationResourceCollection) {
                    $deleteOrganizationResourceCollection = self::deleteResourceCollection($organizationResourceCollection);
                    if (!$deleteOrganizationResourceCollection) {
                        return false;
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function cloneResourceCollection($resourceCollections)
    {
        try {
            $resourceCollection = new ResourceCollection();
            $uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $slug = UtilityHelper::generateSlug($resourceCollections->title.$uuid, $resourceCollection);
            $resourceCollection = $resourceCollections->replicate();
            $resourceCollection->uuid = $uuid;
            $resourceCollection->user_id = auth()->user()->id;
            $resourceCollection->slug = $slug;
            $resourceCollection->save();

            return  $resourceCollection;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
