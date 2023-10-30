<?php

namespace App\Http\Controllers\Api\Manage\ResourceGroup;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\ResourceCollection\UpdateResourceCollectionRequest;
use App\Http\Requests\Manage\ResourceGroup\CreateResourceGroupRequest;
use App\Http\Requests\Manage\ResourceGroup\UpdateResourceGroupRequest;
use App\Http\Resources\Manage\ResourceCollection\ResourceCollectionResource;
use App\Http\Resources\Manage\ResourceGroup\ResourceGroupResource;
use App\Repositories\Api\Manage\ResourceGroup\ResourceGroupRepository;

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
            $upload_cover_image = config('site-settings.default_resource_group_cover_image');
            if ($request->cover_image !== null) {
                $uploaded_cover_image = $this->resourceGroupRepository->uploadResourceGroupCoverImage($request->cover_image);
                if (!$uploaded_cover_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
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
            $createResourceGroup = $this->resourceGroupRepository->createResourceGroup($request, $upload_cover_image, $upload_achievement_image);
            if ($createResourceGroup) {
                return $this->sendResponse(ResourceGroupResource::make($createResourceGroup), __('responses.resource_group_stored_success'), 200);
            }

            return $this->sendError(__('responses.resource_group_stored_failed'), 403);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $responseGroup = $this->resourceGroupRepository->getResourceGroupBasedOnSlug($slug);
            if ($responseGroup) {
                return $this->sendResponse(ResourceGroupResource::make($responseGroup), __('responses.found_resource_group_list'));
            }

            return $this->sendError(__('responses.not_found_resource_group_list'), 404);
        } catch(\Exception $e) {
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
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function delete($slug)
    {
        try {
            $checkResourceGroupSlugExistsOrNot = $this->resourceGroupRepository->getResourceGroupBasedOnSlug($slug);
            if ($checkResourceGroupSlugExistsOrNot == false) {
                return $this->sendError(__('responses.resource_group_slug_not_available'), 404);
            }
            $deleteResourceGroup = $this->resourceGroupRepository->deleteGroupModule($checkResourceGroupSlugExistsOrNot->id);
            if ($deleteResourceGroup) {
                return $this->sendResponse(null, __('responses.resource_group_delete'));
            }

            return $this->sendError(__('responses.resource_group_not_delete'), 400);
        } catch (\Exception $e) {
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

            return $this->sendResponse([], __('responses.resource_group_name_available'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function update($slug, UpdateResourceGroupRequest $request){
        try {
            $checkResourceGroupSlugExistsOrNot = $this->resourceGroupRepository->getResourceGroupBasedOnSlug($slug);
            if ($checkResourceGroupSlugExistsOrNot == false) {
                return $this->sendError(__('responses.resource_group_slug_not_available'), 404);
            }
            $upload_cover_image = str_replace(config('site-settings.aws_url'), '', $checkResourceGroupSlugExistsOrNot->media);
            if ($request->cover_image !== null) {
                $uploaded_cover_image = $this->resourceGroupRepository->uploadResourceGroupCoverImage($request->cover_image);
                if (!$uploaded_cover_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_cover_image = $uploaded_cover_image;
            }
            $upload_achievement_image = str_replace(config('site-settings.aws_url'), '', $checkResourceGroupSlugExistsOrNot->achievement_image);
            if ($request->achievement_image !== null) {
                $uploaded_achievement_image = $this->resourceGroupRepository->uploadAchievementImage($request->achievement_image);
                if (!$uploaded_achievement_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_achievement_image = $uploaded_achievement_image;
            }
            $updateResourceGroup = $this->resourceGroupRepository->updateResourceGroup($slug,$request, $upload_cover_image, $upload_achievement_image);
            if ($updateResourceGroup) {
                return $this->sendResponse(ResourceCollectionResource::make($updateResourceGroup), __('responses.resource_collection_update_success'), 200);
            }
            return $this->sendError(__('responses.resource_collection_update_failed'), 403);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
