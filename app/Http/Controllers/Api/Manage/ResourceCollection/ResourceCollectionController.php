<?php

namespace App\Http\Controllers\Api\Manage\ResourceCollection;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\ResourceCollection\CreateResourceCollectionRequest;
use App\Http\Resources\Manage\ResourceCollection\ResourceCollectionResource;
use App\Repositories\Api\Manage\ResourceCollection\ResourceCollectionRepository;

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
                return $this->sendResponse(ResourceCollectionResource::make($createResourceCollection),__('responses.resource_collection_stored_success'), 200);
            }

            return $this->sendError(__('responses.resource_collection_stored_failed'), 403);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkSlug($slug){
        try {
            $checkResourceCollectionNameExistsOrNot=$this->resourceCollectionRepository->getResourceCollectionBasedOnSlug($slug);
            if($checkResourceCollectionNameExistsOrNot){
                return $this->sendError(__('responses.resource_collection_slug_not_available'));
            }
            return $this->sendResponse([], __('responses.resource_collection_slug_available'), 400);
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkName($title){
        try{

            $checkResourceCollectionNameExistsOrNot = $this->resourceCollectionRepository->checkName($title);
            if ($checkResourceCollectionNameExistsOrNot) {
                return $this->sendError(__('responses.resource_collection_name_not_available'));
            }
            return $this->sendResponse([], __('responses.resource_collection_name_available'), 400);
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
