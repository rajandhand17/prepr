<?php

namespace App\Http\Controllers\Api\Manage\ResourceGroup;

use App\Helpers\ChargebeeHelper;
use App\Helpers\MixpanelHelper;
use App\Helpers\TrackUserProgressHelper;
use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\ResourceGroup\CreateResourceGroupRequest;
use App\Http\Requests\Manage\ResourceGroup\UpdateResourceGroupRequest;
use App\Http\Resources\Manage\ResourceGroup\ResourceGroupListNameResource;
use App\Http\Resources\Manage\ResourceGroup\ResourceGroupResource;
use App\Repositories\Api\Manage\ResourceGroup\ResourceGroupRepository;
use App\Services\LastVisitedActivityModuleService;
use Illuminate\Http\Request;

class ResourceGroupController extends AppBaseController
{
    private $resourceGroupRepository;

    public function __construct(ResourceGroupRepository $resourceGroupRepository)
    {
        $this->resourceGroupRepository = $resourceGroupRepository;
    }

    public function create(CreateResourceGroupRequest $request)
    {
        try {
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            // checks creation limits of the Resource Group
            $checkResourceGroupLimit = ChargebeeHelper::checkComponentLimitBasedOnOrganization($organization->id, 'resourceGroup');
            if ($checkResourceGroupLimit['fetchOrganizationPlanDetails'] !== 'Unlimited') {
                $checkResourceGroupCount = $this->resourceGroupRepository->getResourceGroupCountBasedOnOrganization($checkResourceGroupLimit['organizationId']);
                if ($checkResourceGroupLimit['fetchOrganizationPlanDetails'] <= $checkResourceGroupCount) {
                    return $this->sendError(__('responses.reached_resource_group_limit'), 400);
                }
            }
            $upload_cover_image = config('site-settings.default_resource_group_cover_image');
            if ($request->cover_image !== null) {
                if ($request->media_type == 'image') {
                    $uploaded_cover_image = $this->resourceGroupRepository->uploadResourceGroupCoverImage($request->cover_image);
                    if (!$uploaded_cover_image) {
                        return $this->sendError(__('responses.image_upload_failed'), 400);
                    }
                } elseif ($request->media_type == 'embedded') {
                    $uploaded_cover_image = $request->cover_image;
                }
                $upload_cover_image = $uploaded_cover_image;
            }
            $upload_achievement_image = config('site-settings.default_resource_group_achievement_image');
            if ($request->achievement_image !== null) {
                $uploaded_achievement_image = $this->resourceGroupRepository->uploadAchievementImage($request->achievement_image);
                if (!$uploaded_achievement_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_achievement_image = $uploaded_achievement_image;
            }
            $createResourceGroup = $this->resourceGroupRepository->createResourceGroup($request, $upload_cover_image, $upload_achievement_image, $organization->id);
            if ($createResourceGroup) {
                return $this->sendResponse(ResourceGroupResource::make($createResourceGroup), __('responses.resource_group_stored_success'), 200);
            }

            return $this->sendError(__('responses.resource_group_stored_failed'), 403);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function cloneResourceGroup($slug)
    {
        try {
            // Checking resource group based on slug exists or not
            $getResourceGroup = $this->resourceGroupRepository->getResourceGroupBasedOnSlug($slug);
            if (!$getResourceGroup) {
                return $this->sendError(__('responses.selected_resource_not_found'), 404);
            }
            // Fetching resource group is belongs to current users or not
            if ($getResourceGroup->user_id == auth()->user()->id) {
                return $this->sendError(__('responses.selected_resource_group_already_exists'), 403);
            }
            // Fetching Resource group Based on title and resource current users
            $getResourceCollectionBasedOnTitle = $this->resourceGroupRepository->getResourceGroupBasedOnTitle($getResourceGroup->title);
            if ($getResourceCollectionBasedOnTitle) {
                return $this->sendError(__('responses.selected_resource_group_already_exists'));
            }
            // Cloning resource groups based on title and resource group id
            $cloneResourceModule = $this->resourceGroupRepository->cloneResourceGroup($getResourceGroup->id);
            if ($cloneResourceModule) {
                return $this->sendResponse(ResourceGroupResource::make($cloneResourceModule), __('responses.clone_resource_collection_successfully'));
            }

            return $this->sendError(__('responses.clone_responses_group_failed'), 400);
        } catch(\Exception $e) {
            dd($e);
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $checkResourceGroupExistsOrNot = $this->resourceGroupRepository->getResourceGroupBasedOnSlug($slug);
            if ($checkResourceGroupExistsOrNot) {
                $userData = auth()->user();
                $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
                if (!$organization) {
                    return $this->sendError(__('responses.selected_organization_not_found'), 404);
                }
                if ($checkResourceGroupExistsOrNot->organization_id != $organization->id) {
                    return $this->sendError(__('responses.resource_group_switcher_error'), 403);
                }
                if ($checkResourceGroupExistsOrNot->is_accessible == '0') {
                    return $this->sendError(__('responses.resource_group_not_accessible'), 403);
                }
                // For user progress tracking
                $userId = $userData->id;
                TrackUserProgressHelper::trackResourceGroupUserProgress($checkResourceGroupExistsOrNot, $userId);

                // For last visited activity tracking
                $moduleType = config('constants.module_type.resource_group');
                LastVisitedActivityModuleService::lastVisitedActivityModule($checkResourceGroupExistsOrNot->id, $userId, $moduleType);
                MixpanelHelper::mixpanel_tracking(config('mixpanel.view_resource_group'), $checkResourceGroupExistsOrNot, $userData, request()->ip());

                return $this->sendResponse(ResourceGroupResource::make($checkResourceGroupExistsOrNot), __('responses.found_resource_group_list'));
            }

            return $this->sendError(__('responses.not_found_resource_group_list'), 404);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkSlug($slug)
    {
        try {
            $checkResourceGroupNameExistsOrNot = $this->resourceGroupRepository->getResourceGroupBasedOnSlug($slug);
            if ($checkResourceGroupNameExistsOrNot) {
                return $this->sendError(__('responses.resource_group_slug_not_available'));
            }

            return $this->sendResponse([], __('responses.resource_group_slug_available'), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function delete($slug)
    {
        try {
            $checkResourceGroupExistsOrNot = $this->resourceGroupRepository->getResourceGroupBasedOnSlug($slug);
            if ($checkResourceGroupExistsOrNot == false) {
                return $this->sendError(__('responses.resource_group_slug_not_available'), 404);
            }
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkResourceGroupExistsOrNot->organization_id != $organization->id) {
                return $this->sendError(__('responses.resource_group_switcher_error'), 403);
            }
            if ($checkResourceGroupExistsOrNot->is_accessible == '0') {
                return $this->sendError(__('responses.resource_group_not_accessible'), 403);
            }
            $deleteResourceGroup = $this->resourceGroupRepository->deleteGroupModule($checkResourceGroupExistsOrNot->id);
            if ($deleteResourceGroup) {
                return $this->sendResponse(null, __('responses.resource_group_delete'));
            }

            return $this->sendError(__('responses.resource_group_not_delete'), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkName($title)
    {
        try {
            $checkResourceGroupNameExistsOrNot = $this->resourceGroupRepository->checkName($title);
            if ($checkResourceGroupNameExistsOrNot) {
                return $this->sendError(__('responses.resource_group_name_not_available'));
            }

            return $this->sendResponse([], __('responses.resource_group_name_available'), 200);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function update($slug, UpdateResourceGroupRequest $request)
    {
        try {
            $checkResourceGroupExistsOrNot = $this->resourceGroupRepository->getResourceGroupBasedOnSlug($slug);
            if ($checkResourceGroupExistsOrNot == false) {
                return $this->sendError(__('responses.resource_group_slug_not_available'), 404);
            }
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkResourceGroupExistsOrNot->organization_id != $organization->id) {
                return $this->sendError(__('responses.resource_group_switcher_error'), 403);
            }

            if ($checkResourceGroupExistsOrNot->is_accessible == '0') {
                return $this->sendError(__('responses.resource_group_not_accessible'), 403);
            }
            $upload_cover_image = str_replace(config('site-settings.aws_url'), '', $checkResourceGroupExistsOrNot->media);
            if ($request->cover_image !== null) {
                if ($request->media_type == 'image') {
                    $uploaded_cover_image = $this->resourceGroupRepository->uploadResourceGroupCoverImage($request->cover_image);
                    if (!$uploaded_cover_image) {
                        return $this->sendError(__('responses.image_upload_failed'), 400);
                    }
                } elseif ($request->media_type == 'embedded') {
                    $uploaded_cover_image = $request->cover_image;
                }
                $upload_cover_image = $uploaded_cover_image;
            }
            $upload_achievement_image = str_replace(config('site-settings.aws_url'), '', $checkResourceGroupExistsOrNot->achievement_image);
            if ($request->achievement_image !== null) {
                $uploaded_achievement_image = $this->resourceGroupRepository->uploadAchievementImage($request->achievement_image);
                if (!$uploaded_achievement_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_achievement_image = $uploaded_achievement_image;
            }
            $updateResourceGroup = $this->resourceGroupRepository->updateResourceGroup($slug, $request, $upload_cover_image, $upload_achievement_image, $organization->id);
            if ($updateResourceGroup) {
                return $this->sendResponse(ResourceGroupResource::make($updateResourceGroup), __('responses.resource_collection_update_success'), 200);
            }

            return $this->sendError(__('responses.resource_collection_update_failed'), 403);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }

            $resourceGroup = $this->resourceGroupRepository->getResourceGroupList($request, $organization);
            if ($resourceGroup) {
                $response = [
                    'total_count'  => $resourceGroup->total(),
                    'per_page'     => $resourceGroup->perPage(),
                    'count'        => $resourceGroup->count(),
                    'current_page' => $resourceGroup->currentPage(),
                    'total_pages'  => $resourceGroup->lastPage(),
                    'list'         => ResourceGroupResource::collection($resourceGroup),
                ];

                return $this->sendResponse($response, __('responses.found_resource_group_list'));
            }

            return $this->sendError(__('responses.not_found_resource_group_list'), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getList(Request $request)
    {
        try {
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            $getResourceGroupListName = $this->resourceGroupRepository->getResourceGroupListName($request, $organization);
            if ($getResourceGroupListName) {
                $response = ResourceGroupListNameResource::collection($getResourceGroupListName);
            }

            return $this->sendResponse($getResourceGroupListName, __('responses.found_resource_group_list'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
