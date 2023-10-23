<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Challenge;
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
}
