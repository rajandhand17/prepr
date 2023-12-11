<?php

namespace App\Http\Controllers\Api\Manage\ResourceCollection;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\ResourceCollection\CreateResourceCollectionRequest;
use App\Http\Requests\Manage\ResourceCollection\UpdateResourceCollectionRequest;
use App\Http\Resources\Manage\ResourceCollection\ResourceCollectionListNameResource;
use App\Http\Resources\Manage\ResourceCollection\ResourceCollectionResource;
use App\Repositories\Api\Manage\ResourceCollection\ResourceCollectionRepository;
use App\Services\Manage\OrganizationService;
use Illuminate\Http\Request;

class ResourceCollectionController extends AppBaseController
{
    private $resourceCollectionRepository;

    public function __construct(ResourceCollectionRepository $resourceCollectionRepository)
    {
        $this->resourceCollectionRepository = $resourceCollectionRepository;
    }

    public function create(CreateResourceCollectionRequest $request)
    {
        try {
            $upload_cover_image = config('site-settings.default_resource_collection_cover_image');
            if ($request->cover_image !== null) {
                $uploaded_cover_image = $this->resourceCollectionRepository->uploadResourceCollectionCoverImage($request->cover_image);
                if (!$uploaded_cover_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_cover_image = $uploaded_cover_image;
            }
            $createResourceCollection = $this->resourceCollectionRepository->createResourceCollection($request, $upload_cover_image);
            if ($createResourceCollection) {
                return $this->sendResponse(ResourceCollectionResource::make($createResourceCollection), __('responses.resource_collection_stored_success'), 200);
            }

            return $this->sendError(__('responses.resource_collection_stored_failed'), 403);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkSlug($slug)
    {
        try {
            $checkResourceCollectionNameExistsOrNot = $this->resourceCollectionRepository->getResourceCollectionBasedOnSlug($slug);
            if ($checkResourceCollectionNameExistsOrNot) {
                return $this->sendError(__('responses.resource_collection_slug_not_available'));
            }

            return $this->sendResponse([], __('responses.resource_collection_slug_available'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkName($title)
    {
        try {
            $checkResourceCollectionNameExistsOrNot = $this->resourceCollectionRepository->checkName($title);
            if ($checkResourceCollectionNameExistsOrNot) {
                return $this->sendError(__('responses.resource_collection_name_not_available'));
            }

            return $this->sendResponse([], __('responses.resource_collection_name_available'), 400);
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

    public function update($slug, UpdateResourceCollectionRequest $request)
    {
        try {
            $checkResourceCollectionSlugExistsOrNot = $this->resourceCollectionRepository->getResourceCollectionBasedOnSlug($slug);
            if ($checkResourceCollectionSlugExistsOrNot == false) {
                return $this->sendError(__('responses.resource_collection_slug_not_found'), 404);
            }
            $upload_cover_image = str_replace(config('site-settings.aws_url'), '', $checkResourceCollectionSlugExistsOrNot->media);
            if ($request->cover_image !== null) {
                $uploaded_cover_image = $this->resourceCollectionRepository->uploadResourceCollectionCoverImage($request->cover_image);
                if (!$uploaded_cover_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_cover_image = $uploaded_cover_image;
            }
            $updateResourceCollection = $this->resourceCollectionRepository->updateResourceCollection($slug, $request, $upload_cover_image);
            if ($updateResourceCollection) {
                return $this->sendResponse(ResourceCollectionResource::make($updateResourceCollection), __('responses.resource_collection_update_success'), 200);
            }

            return $this->sendError(__('responses.resource_collection_update_failed'), 403);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function index(Request $request)
    {
        $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
        if (!$organization) {
            return $this->sendError(__('responses.organization_not_found'), 404);
        }
        $resourceCollection = $this->resourceCollectionRepository->getResourceCollectionList($request, $organization);

        if ($resourceCollection) {
            $response = [
                'total_count'  => $resourceCollection->total(),
                'per_page'     => $resourceCollection->perPage(),
                'count'        => $resourceCollection->count(),
                'current_page' => $resourceCollection->currentPage(),
                'total_pages'  => $resourceCollection->lastPage(),
                'list'         => ResourceCollectionResource::collection($resourceCollection),
            ];

            return $this->sendResponse($response, __('responses.found_resource_collection_list'));
        }

        return $this->sendError(__('responses.not_found_resource_collection_view'), 400);
    }

    public function delete($slug)
    {
        try {
            $checkResourceCollectionSlugExistsOrNot = $this->resourceCollectionRepository->getResourceCollectionBasedOnSlug($slug);
            if ($checkResourceCollectionSlugExistsOrNot == false) {
                return $this->sendError(__('responses.resource_collection_slug_not_found'), 404);
            }
            $responseCollectionDelete = $this->resourceCollectionRepository->deleteResourceCollection($checkResourceCollectionSlugExistsOrNot->id);
            if ($responseCollectionDelete) {
                return $this->sendResponse(null, __('responses.resource_collection_delete'));
            }

            return $this->sendError(__('responses.resource_collection_not_delete'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getList(Request $request)
    {
        try {
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_found'), 404);
            }
            $resourceCollection = $this->resourceCollectionRepository->getListName($request, $organization);

            if ($resourceCollection) {
                return $this->sendResponse(ResourceCollectionListNameResource::collection($resourceCollection), __('responses.found_resource_collection_list'));
            }

            return $this->sendError(__('responses.not_found_resource_collection_view'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
