<?php

namespace App\Http\Controllers\Api\Public\ResourceGroup;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\ResourceGroup\ResourceGroupResource;
use App\Repositories\Api\Public\ResourceGroup\ResourceGroupRepository;
use Illuminate\Http\Request;

class ResourceGroupController extends AppBaseController
{
    private $resourceGroupRepository;

    public function __construct(ResourceGroupRepository $resourceGroupRepository)
    {
        $this->resourceGroupRepository = $resourceGroupRepository;
    }

    public function index(Request $request)
    {
        try {
            $resourceGroup = $this->resourceGroupRepository->getResourceGroupList($request);
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
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $resourceGroup = $this->resourceGroupRepository->getResourceGroupBasedOnSlug($slug);
            if ($resourceGroup) {
                return $this->sendResponse(ResourceGroupResource::make($resourceGroup), __('responses.found_resource_group_list'));
            }

            return $this->sendError(__('responses.not_found_resource_group_list'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function socialActivity($slug, $action)
    {
        try {
            $checkResourceGroupSlugExistsOrNot = $this->resourceGroupRepository->getResourceGroupBasedOnSlug($slug);
            if ($checkResourceGroupSlugExistsOrNot !== null) {
                $getColumnNameValue = $this->resourceGroupRepository->getColumnNameValue($action);
                if (!$getColumnNameValue) {
                    return $this->sendError(__('responses.handler_bad_request'), 400);
                }
                $checkActivity = $this->resourceGroupRepository->checkSocialActivity($checkResourceGroupSlugExistsOrNot->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                $action = str_replace('-', '_', $action);
                if ($checkActivity === true) {
                    return $this->sendError(__('responses.already_'.$action.'_resource_group'), 400);
                }
                $resourceGroup = $this->resourceGroupRepository->captureSocialActivity($checkResourceGroupSlugExistsOrNot->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                if ($resourceGroup) {
                    return $this->sendResponse([], __('responses.'.$action.'_resource_group_successfully'));
                }
            }

            return $this->sendError(__('responses.resource_group_slug_not_found'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
