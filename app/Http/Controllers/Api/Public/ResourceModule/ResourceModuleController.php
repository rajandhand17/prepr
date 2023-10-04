<?php

namespace App\Http\Controllers\Api\Public\ResourceModule;

use App\Http\Controllers\AppBaseController;
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

    public function index(Request $request){
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
            $responseView = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if ($responseView) {
                return $this->sendResponse(ResourceModuleResource::make($responseView), __('responses.found_resource_module_list'));
            }
            return $this->sendError(__('responses.not_found_resource_module_view'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addRating($slug,$request){
        try {
            $checkResourceModuleSlugExistsOrNot = $this->resourceModuleRepository->checkSlug($slug);
            if ($checkResourceModuleSlugExistsOrNot == false) {
                return $this->sendError(__('responses.resource_module_slug_not_found'), 404);
            }
            $addRating = $this->resourceModuleRepository->addRating($checkResourceModuleSlugExistsOrNot->id,$request);
            if($addRating){
               // return $this->sendResponse();
            }
            return $this->sendError(__('responses.not_found_resource_module_view'), 404);

        }catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
