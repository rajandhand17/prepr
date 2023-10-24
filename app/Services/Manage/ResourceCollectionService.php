<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\ResourceCollection;
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
}
