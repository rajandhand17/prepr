<?php

namespace App\Http\Controllers\Api\Public\ResourceCollection;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Public\ResourceCollection\AddRatingRequest;
use App\Http\Resources\Public\ResourceCollection\ResourceCollectionResource;
use App\Repositories\Api\Public\ResourceCollection\ResourceCollectionRepository;
use Illuminate\Http\Request;

class ResourceCollectionController extends AppBaseController
{
    private $resourceCollectionRepository;

    public function __construct(ResourceCollectionRepository $resourceCollectionRepository)
    {
        $this->resourceCollectionRepository = $resourceCollectionRepository;
    }

    public function index(Request $request)
    {
        try {
            $responseCollectionList = $this->resourceCollectionRepository->getResourceCollectionList($request);
            if ($responseCollectionList) {
                $response = [
                    'total_count'  => $responseCollectionList->total(),
                    'per_page'     => $responseCollectionList->perPage(),
                    'count'        => $responseCollectionList->count(),
                    'current_page' => $responseCollectionList->currentPage(),
                    'total_pages'  => $responseCollectionList->lastPage(),
                    'list'         => ResourceCollectionResource::collection($responseCollectionList),
                ];

                return $this->sendResponse($response, __('responses.found_resource_module_list'));
            }

            return $this->sendError(__('responses.not_found_resource_module_list'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $responseCollection = $this->resourceCollectionRepository->getResourceCollectionBasedOnSlug($slug);
            if ($responseCollection) {
                return $this->sendResponse(ResourceCollectionResource::make($responseCollection), __('responses.found_resource_collection_list'));
            }

            return $this->sendError(__('responses.not_found_resource_collection_view'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function socialActivity($slug, $action)
    {
        try {
            $checkResourceCollectionSlugExistsOrNot = $this->resourceCollectionRepository->getResourceCollectionBasedOnSlug($slug);
            if ($checkResourceCollectionSlugExistsOrNot !== null) {
                $getColumnNameValue = $this->resourceCollectionRepository->getColumnNameValue($action);
                if (!$getColumnNameValue) {
                    return $this->sendError(__('responses.handler_bad_request'), 400);
                }
                $checkActivity = $this->resourceCollectionRepository->checkSocialActivity($checkResourceCollectionSlugExistsOrNot->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                $action = str_replace('-', '_', $action);
                if ($checkActivity === true) {
                    return $this->sendError(__('responses.already_'.$action.'_resource_collection'), 400);
                }
                $resourceCollection = $this->resourceCollectionRepository->captureSocialActivity($checkResourceCollectionSlugExistsOrNot->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                if ($resourceCollection) {
                    return $this->sendResponse([], __('responses.'.$action.'_resource_collection_successfully'));
                }
            }

            return $this->sendError(__('responses.resource_module_slug_not_found'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addRating($slug, AddRatingRequest $request)
    {
        try {
            $checkResourceCollectionSlugExistsOrNot = $this->resourceCollectionRepository->getResourceCollectionBasedOnSlug($slug);
            if ($checkResourceCollectionSlugExistsOrNot == false) {
                return $this->sendError(__('responses.resource_collection_slug_not_found'), 404);
            }
            $addRating = $this->resourceCollectionRepository->addRating($checkResourceCollectionSlugExistsOrNot->id, $request);
            if ($addRating) {
                return $this->sendResponse(null, __('responses.resource_collection_rating_successfully'));
            }

            return $this->sendError(__('responses.resource_collection_rating_failed'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
