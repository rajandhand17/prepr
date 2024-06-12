<?php

namespace App\Http\Controllers\Api\Public\ResourceModule;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Public\ResourceModule\AddRatingRequest;
use App\Http\Resources\Public\ResourceModule\ResourceModuleResource;
use App\Repositories\Api\Public\ResourceModule\ResourceModuleRepository;
use Illuminate\Http\Request;

class ResourceModuleController extends AppBaseController
{
    private $resourceModuleRepository;

    public function __construct(ResourceModuleRepository $resourceModuleRepository)
    {
        $this->resourceModuleRepository = $resourceModuleRepository;
    }

    public function index(Request $request)
    {
        try {
            $responseModuleList = $this->resourceModuleRepository->getResourceModuleList($request);
            if ($responseModuleList) {
                $response = [
                    'total_count'  => $responseModuleList->total(),
                    'per_page'     => $responseModuleList->perPage(),
                    'count'        => $responseModuleList->count(),
                    'current_page' => $responseModuleList->currentPage(),
                    'total_pages'  => $responseModuleList->lastPage(),
                    'list'         => ResourceModuleResource::collection($responseModuleList),
                ];

                return $this->sendResponse($response, __('responses.found_resource_module_list'));
            }

            return $this->sendError(__('responses.not_found_resource_module_list'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $checkResourceModuleExistsOrNot = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if ($checkResourceModuleExistsOrNot) {
                if ($checkResourceModuleExistsOrNot->is_accessible == '0') {
                    return $this->sendError(__('responses.resource_module_not_accessible'), 403);
                }

                return $this->sendResponse(ResourceModuleResource::make($checkResourceModuleExistsOrNot), __('responses.found_resource_module_list'));
            }

            return $this->sendError(__('responses.not_found_resource_module_view'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addRating($slug, AddRatingRequest $request)
    {
        try {
            $checkResourceModuleExistsOrNot = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if ($checkResourceModuleExistsOrNot == false) {
                return $this->sendError(__('responses.resource_module_slug_not_found'), 404);
            }
            if ($checkResourceModuleExistsOrNot->is_accessible == '0') {
                return $this->sendError(__('responses.resource_module_not_accessible'), 403);
            }
            $addRating = $this->resourceModuleRepository->addRating($checkResourceModuleExistsOrNot->id, $request);
            if ($addRating) {
                return $this->sendResponse(null, __('responses.resource_module_rating_successfully'));
            }

            return $this->sendError(__('responses.not_found_resource_module_view'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function socialActivity($slug, $action)
    {
        try {
            $checkResourceModuleExistsOrNot = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if ($checkResourceModuleExistsOrNot !== null) {
                if ($checkResourceModuleExistsOrNot->is_accessible == '0') {
                    return $this->sendError(__('responses.resource_module_not_accessible'), 403);
                }
                $getColumnNameValue = $this->resourceModuleRepository->getColumnNameValue($action);
                if (!$getColumnNameValue) {
                    return $this->sendError(__('responses.handler_bad_request'), 400);
                }
                $checkActivity = $this->resourceModuleRepository->checkSocialActivity($checkResourceModuleExistsOrNot->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                $action = str_replace('-', '_', $action);
                if ($checkActivity === true) {
                    return $this->sendError(__('responses.already_'.$action.'_resource_module'), 400);
                }
                $resourceModule = $this->resourceModuleRepository->captureSocialActivity($checkResourceModuleExistsOrNot->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                if ($resourceModule) {
                    return $this->sendResponse([], __('responses.'.$action.'_resource_module_successfully'));
                }
            }

            return $this->sendError(__('responses.resource_module_slug_not_found'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
