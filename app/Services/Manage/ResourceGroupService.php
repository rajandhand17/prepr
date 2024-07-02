<?php

namespace App\Services\Manage;

use App\Events\ResourceGroup\DeleteResourceGroupAssociatedData;
use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Duration;
use App\Models\Levels;
use App\Models\ResourceGroup;
use Exception;
use HiFolks\RandoPhp\Randomize;

class ResourceGroupService
{
    public function getResourceGroupCountBasedOnOrganization($organizationId)
    {
        try {
            $resourceGroup_count = ResourceGroup::where(['organization_id' => $organizationId, 'is_auto_created' => '0'])->count();

            return $resourceGroup_count;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function uploadResourceGroupCoverImage($cover_image)
    {
        try {
            $upload_resource_group_cover_image = FileUploadHelper::uploadImageToS3($cover_image, 'resource_group');
            if ($upload_resource_group_cover_image == false) {
                return false;
            }

            return $upload_resource_group_cover_image;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createResourceGroup($request, $upload_cover_image, $organizationId)
    {
        try {
            $status = config('constants.resource_group_status.draft');
            switch($request->status) {
                case 'publish':
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
            $resourceGroup = new ResourceGroup();
            $slug = UtilityHelper::generateSlug($request->title, $resourceGroup);
            $resourceGroup->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $resourceGroup->language = $request->language;
            $resourceGroup->user_id = auth()->user()->id;
            $resourceGroup->organization_id = $organizationId;
            $resourceGroup->title = $request->title;
            $resourceGroup->slug = $slug;
            $resourceGroup->description = $request->description;
            $resourceGroup->media_type = 'image';
            $resourceGroup->media = $upload_cover_image;
            $resourceGroup->level = $request->level;
            $resourceGroup->duration = $request->duration;
            $resourceGroup->privacy = $privacy;
            $resourceGroup->status = $status;
            $resourceGroup->save();

            return $resourceGroup;
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

    public static function deleteGroupModule($resource_group_id)
    {
        try {
            $resourceModule = ResourceGroup::find($resource_group_id)->delete();
            if ($resourceModule) {
                $associatedResourceModule = event(new DeleteResourceGroupAssociatedData($resource_group_id));

                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkName($title)
    {
        try {
            return ResourceGroup::select('id')->where('title', $title)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateResourceGroup($slug, $request, $upload_cover_image, $organizationId)
    {
        try {
            $resourceGroup = ResourceGroup::where('slug', $slug)->first();
            $status = $resourceGroup->status;
            $privacy = $resourceGroup->privacy;
            switch($request->status) {
                case 'publish':
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
            $resourceGroup->language = ($request->has('language')) ? $request->language : $resourceGroup->language;
            $resourceGroup->organization_id = $organizationId;
            $resourceGroup->title = ($request->has('title')) ? $request->title : $resourceGroup->title;
            $resourceGroup->description = ($request->has('description')) ? $request->description : $resourceGroup->description;
            $resourceGroup->media_type = ($request->has('media_type')) ? $request->media_type : $resourceGroup->media_type;
            $resourceGroup->media = ($upload_cover_image != null) ? $upload_cover_image : $resourceGroup->cover_image;
            $resourceGroup->level = ($request->has('level')) ? $request->level : $resourceGroup->level;
            $resourceGroup->duration = ($request->has('duration')) ? $request->duration : $resourceGroup->duration;
            $resourceGroup->privacy = $privacy;
            $resourceGroup->status = $status;
            $resourceGroup->save();

            return $resourceGroup;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceGroupList($request, $organization)
    {
        try {
            $resourceGroupList = ResourceGroup::select()->where('organization_id', '=', $organization->id);
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
                $status = ($request->status == 'draft') ? '0' : (($request->status == 'publish') ? '1' : (($request->status == 'archive') ? '2' : '3'));
                $resourceGroupList = $resourceGroupList->where('resource_groups.status', $status);
            } else {
                $resourceGroupList = $resourceGroupList->where('resource_groups.status', '1');
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

    public static function getResourceGroupBasedOnId($id)
    {
        try {
            return ResourceGroup::where(['id' => $id, 'is_accessible' => '1'])->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getResourceGroupListName($request, $organization)
    {
        try {
            $resourceGroupList = ResourceGroup::select('uuid', 'title', 'media')->where(['organization_id' => $organization->id, 'is_accessible' => '1']);
            $resourceGroupList = self::filterResourceGroupList($resourceGroupList, $request);
            $limit = config('site-settings.listing_limit');

            return $resourceGroupList->limit($limit)->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceGroupBasedOnUUID($uUID)
    {
        try {
            return ResourceGroup::select('id', 'uuid', 'title', 'media', 'slug', 'description')->where('UUID', $uUID)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceGroupBasedOnUUIDArray($resourceGroupUUIDArray)
    {
        try {
            $resourceGroupIds = ResourceGroup::whereIn('uuid', $resourceGroupUUIDArray)->pluck('id')->all();
            if ($resourceGroupIds != null) {
                return $resourceGroupIds;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteOrganizationResourceGroup($organizationId)
    {
        try {
            $fetchOrganizationResourceGroups = ResourceGroup::where('organization_id', $organizationId)->pluck('id');
            if (!empty($fetchOrganizationResourceGroups)) {
                foreach ($fetchOrganizationResourceGroups as $organizationResourceGroup) {
                    $deleteOrganizationResourceGroup = self::deleteGroupModule($organizationResourceGroup);
                    if (!$deleteOrganizationResourceGroup) {
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
}
