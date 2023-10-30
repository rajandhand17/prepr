<?php

namespace App\Services\Manage;

use App\Events\ResourceGroup\DeleteResourceGroupAssociatedData;
use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\ResourceCollection;
use App\Models\ResourceGroup;
use HiFolks\RandoPhp\Randomize;

class ResourceGroupService
{
    public static function uploadResourceGroupCoverImage($cover_image)
    {
        try {
            $upload_resource_group_cover_image = FileUploadHelper::uploadImageToS3($cover_image, 'resource_group');
            if ($upload_resource_group_cover_image == false) {
                return false;
            }

            return $upload_resource_group_cover_image;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function createResourceGroup($request, $upload_cover_image)
    {
        try {
            $status = config('constants.resource_group_status.draft');
            switch($request->status) {
                case 'published':
                    $status = config('constants.resource_group_status.publish');
                    break;
                case 'archive':
                    $status = config('constants.resource_group_status.archive');
                    break;
                default:
                    $status = config('constants.resource_group_status.draft');
                    break;
            }

            switch ($request->privacy) {
                case 'no':
                    $privacy = config('constants.resource_group_privacy.no');
                    break;
                case 'yes':
                    $privacy = config('constants.resource_group_privacy.yes');
                    break;
                default:
                    $privacy = null;
            }
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            $resourceGroup = new ResourceGroup();
            $slug = UtilityHelper::generateSlug($request->title, $resourceGroup);
            $resourceGroup->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $resourceGroup->language = $request->language;
            $resourceGroup->user_id = auth()->user()->id;
            $resourceGroup->organization_id = $organization->id;
            $resourceGroup->title = $request->title;
            $resourceGroup->slug = $slug;
            $resourceGroup->description = $request->description;
            $resourceGroup->media_type = $request->media_type;
            $resourceGroup->media = $upload_cover_image;
            $resourceGroup->level = $request->level;
            $resourceGroup->duration = $request->duration;
            $resourceGroup->privacy = $privacy;
            $resourceGroup->status = $status;
            $resourceGroup->save();

            return $resourceGroup;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getResourceGroupBasedOnSlug($slug)
    {
        try {
            return ResourceGroup::where('slug', $slug)->first();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteGroupModule($resource_group_id)
    {
        try {
            $resourceModule = ResourceGroup::find($resource_group_id)->delete();
            if ($resourceModule) {
                $associatedResourceModule = event(new DeleteResourceGroupAssociatedData($resource_group_id));

                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkName($title){
        try {
            return ResourceGroup::select('id')->where('title', $title)->first();
        }catch (\Exception $e) {
            return false;
        }
    }
}
