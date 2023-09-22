<?php

namespace App\Http\Controllers\Api\Manage\ResourceModule;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\Resource\CreateResourceRequest;
use App\Http\Resources\Manage\ResourceModule\ResourceModuleResource;
use App\Repositories\Api\Manage\ResourceModule\ResourceModuleDetailRepository;
use Illuminate\Http\Request;

class ResourceModuleController extends AppBaseController
{
    private $resourceModuleRepository;

    private $responseModuleDetailsRepository;

    public function __construct(ResourceModuleDetailRepository $resourceModuleRepository)
    {
        $this->resourceModuleRepository = $resourceModuleRepository;
    }

    public function index(Request $request){
        try{
            $responseModuleList=$this->resourceModuleRepository->getResourceModuleList($request);
            if ($responseModuleList){
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
            $responseView = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if ($responseView) {
                return $this->sendResponse(ResourceModuleResource::make($responseView), __('responses.found_resource_module_list'));
            }
            return $this->sendError(__('responses.not_found_resource_module_view'), 404);
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function delete($slug)
    {
        try {
            $checkResourceModuleSlugExistsOrNot = $this->resourceModuleRepository->checkSlug($slug);
            if ($checkResourceModuleSlugExistsOrNot == false) {
                return $this->sendError(__('responses.resource_module_slug_not_found'), 404);
            }
            $deleteResourceModule = $this->resourceModuleRepository->delete($slug);
            if($deleteResourceModule) {
                return $this->sendResponse(null, __('responses.resource_module_delete'));
            }
            return $this->sendError(__('responses.resource_module_not_delete'), 400);
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function deleteMedia($resource_module_id)
    {
        try {
            $deleteResourceModule = $this->resourceModuleRepository->deleteMedia($resource_module_id);
            if($deleteResourceModule) {
                return $this->sendResponse(null, __('responses.resource_module_delete'),200);
            }
            return $this->sendError(__('responses.resource_module_not_delete'), 400);
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function checkName($title)
    {
        try {
            $checkResourceModuleNameExistsOrNot = $this->resourceModuleRepository->checkName($title);
            if ($checkResourceModuleNameExistsOrNot) {
                return $this->sendError(__('responses.resource_module_name_not_available'));
            }

            return $this->sendResponse([], __('responses.resource_module_name_available'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function checkSlug($slug){
        try{
                $checkResourceModuleNameExistsOrNot=$this->resourceModuleRepository->checkSlug($slug);
                if ($checkResourceModuleNameExistsOrNot) {
                    return $this->sendError(__('responses.resource_module_slug_not_available'));
                }
                return $this->sendResponse([], __('responses.resource_module_slug_available'), 400);
            }catch(\Exception $e){
                return $this->sendError(__('responses.send_error'),500);
            }
    }

    public function createResourceModule(CreateResourceRequest $request){
        try{
            $upload_media = config('site-settings.default_resource_module_cover_image');
            if ($request->media !== null){
                $uploaded_media = $this->resourceModuleRepository->uploadResourceModuleMedia($request->media);
                if (!$uploaded_media) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_media = $uploaded_media;
            }
            $createResourceModule=$this->resourceModuleRepository->createResourceModule($request,$upload_media);
            if($createResourceModule){
                return $this->sendResponse(__('responses.resource_module_stored_success'),200);
            }
            return $this->sendError(__('responses.resource_module_stored_failed'), 403);
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function details(Request $request){
        try{

        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function addLinks(){
        try{

        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }
}
