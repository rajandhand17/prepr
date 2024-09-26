<?php

namespace App\Services\Manage;

use App\Events\ResourceGroup\DeleteResourceGroupAssociatedData;
use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\ResourceGroup;
use App\Services\ModuleCompletionStatusService;
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
            switch ($request->status) {
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
            $media_type = config('constants.resource_media_type.image');
            switch ($request->media_type) {
                case 'image':
                    $media_type = config('constants.resource_media_type.image');
                    break;
                case 'embedded':
                    $media_type = config('constants.resource_media_type.embedded');
                    break;
                default:
                    $media_type = config('constants.resource_media_type.image');
            }
            switch ($request->privacy) {
                case 'public':
                    $privacy = config('constants.resource_group_privacy.no');
                    break;
                case 'private':
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
            $resourceGroup->category_id = $request->category_id;
            $resourceGroup->title = $request->title;
            $resourceGroup->slug = $slug;
            $resourceGroup->description = $request->description;
            $resourceGroup->media_type = $media_type;
            $resourceGroup->media = $upload_cover_image;
            $resourceGroup->level = $request->level;
            $resourceGroup->duration = $request->duration;
            $resourceGroup->privacy = $privacy;
            $resourceGroup->status = $status;
            $resourceGroup->is_accessible = '1';
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

    public static function getResourceGroupBasedOnTitle($title)
    {
        try {
            $userId = auth()->id();
            $resourceGroup = ResourceGroup::where(['title'=>$title, 'user_id'=>auth()->user()->id])->first();

            return $resourceGroup;
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
            switch ($request->status) {
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
                case 'public':
                    $privacy = config('constants.resource_group_privacy.no');
                    break;
                case 'private':
                    $privacy = config('constants.resource_group_privacy.yes');
                    break;
                default:
                    $privacy = null;
            }
            $resourceGroup->language = ($request->has('language')) ? $request->language : $resourceGroup->language;
            $resourceGroup->organization_id = $organizationId;
            $resourceGroup->category_id = ($request->has('category_id')) ? $request->category_id : $resourceGroup->category_id;
            $resourceGroup->title = ($request->has('title')) ? $request->title : $resourceGroup->title;
            $resourceGroup->description = ($request->has('description')) ? $request->description : $resourceGroup->description;
            $resourceGroup->media_type = ($request->has('media_type')) ? $media_type : $resourceGroup->media_type;
            $resourceGroup->media = ($upload_cover_image != null) ? $upload_cover_image : $resourceGroup->cover_image;
            $resourceGroup->level = ($request->has('level')) ? $request->level : $resourceGroup->level;
            $resourceGroup->duration = ($request->has('duration')) ? $request->duration : $resourceGroup->duration;
            $resourceGroup->privacy = $privacy;
            $resourceGroup->status = $status;
            $resourceGroup->is_accessible = '1';
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
                $resourceGroupList = $resourceGroupList->whereSearchFilter($request->search ?? '');
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

            if ($request->has('privacy') && !empty($request->privacy)) {
                $privacy = null;
                switch ($request->privacy) {
                    case 'private':
                        $privacy = config('constants.resource_group_privacy.yes');
                        break;
                    case 'public':
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

            if ($request->has('level_id') && $request->level_id && is_array($request->level_id)) {
                $resourceGroupList = $resourceGroupList->whereIn('level', $request->level_id);
            }
            if ($request->has('duration_id') && $request->duration_id && is_array($request->duration_id)) {
                $resourceGroupList = $resourceGroupList->whereIn('duration', $request->duration_id);
            }
            if ($request->has('rating') && !empty($request->rating)) {
                $getResourceGroupList = ResourceGroupRatingService::getResourceGroupBasedOnRating($request->rating);
                $resourceGroupList = $resourceGroupList->whereIn('id', $getResourceGroupList->pluck('resource_group_id'));
            }
            if ($request->has('type') && $request->type !== null) {
                $resourceGroupType = ResourceGroupTypeModesService::getResourceGroupBasedOnType($request->type);
                $resourceGroupList = $resourceGroupList->whereIn('id', $resourceGroupType->pluck('resource_group_id'));
            }

            if ($request->has('progress') && !empty($request->progress)) {
                $resourceGroupProgress = [];
                $moduleType = config('constants.module_completion_statuses_types.resource_group');
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
                $resourceGroupList = $resourceGroupList->whereIn('id', $resourceGroupProgress->pluck('module_id'));
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
            $resourceGroupList = ResourceGroup::select('uuid', 'title', 'media')->where(['organization_id' => $organization->id, 'status' => '1', 'is_accessible' => '1']);
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

    public static function getResourceGroupBasedOnIdArray($resourceGroupIdArray)
    {
        try {
            $resourceGroupIds = ResourceGroup::whereIn('id', $resourceGroupIdArray)->pluck('id')->all();
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

    public static function getResourcesWithRelations($id)
    {
        try {
            return ResourceGroup::with('skills_group_stack', 'resource_group_achievement', 'component_association', 'resource_group_type_mode')->find($id);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function cloneResourceGroup($resourceGroupData, $organization)
    {
        try {
            $resourceGroup = new ResourceGroup();
            $slug = UtilityHelper::generateSlug($organization->title.' '.$resourceGroupData->title, $resourceGroup);
            $title = UtilityHelper::generateTitle($organization->title.' '.$resourceGroupData->title, $resourceGroup);
            $resourceGroup = $resourceGroupData->replicate();
            $resourceGroup->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $resourceGroup->title = $title;
            $resourceGroup->slug = $slug;
            if ($resourceGroupData->media_type == '') {
                $resourceGroup->media_type = '0';
            }
            $resourceGroup->user_id = auth()->user()->id;
            $resourceGroup->organization_id = $organization->id;
            $resourceGroup->save();

            return $resourceGroup;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchResourceGroupReportBasedOnOrganization($organizationId)
    {
        try {
            $fetchResourceGroup = ResourceGroup::where(['organization_id' => $organizationId, 'status' => '1', 'is_accessible' => '1'])->get();

            return $fetchResourceGroup;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
