<?php

namespace App\Http\Controllers\Api\Manage\ResourceGroup;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\ResourceGroup\CreateResourceGroupRequest;
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
}
