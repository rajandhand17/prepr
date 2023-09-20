<?php

namespace App\Http\Controllers\Api\Manage\ResourceModule;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Manage\Organization\OrganizationResource;
use App\Http\Resources\Manage\ResourceModule\ResourceModuleResource;
use App\Repositories\Api\Manage\ResourceModule\ResourceModuleRepository;
use Illuminate\Http\Request;

class ResourceModuleController extends AppBaseController
{
    private $resourceModuleRepository;

    public function __construct(ResourceModuleRepository $resourceModuleRepository){
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
                    'list'         => ResourceModuleResource::make($responseModuleList),
                ];
                return $this->sendResponse($response, __('responses.found_resource_module_list'));
            }
            return $this->sendError(__('responses.not_found_resource_module_list'), 400);
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug){
        try{
            $responseView=$this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if($responseView){
                return $this->sendResponse(ResourceModuleResource::make($responseView), __('responses.found_resource_module_list'));
            }
            return $responseView;
        }catch(\Exception $e){
            return $this->sendError(__('response.send_error'),500);
        }
    }

    public function delete($slug){
        try{
            $checkResourceModuleSlugExistsOrNot = $this->resourceModuleRepository->checkSlug($slug);
            if ($checkResourceModuleSlugExistsOrNot == false) {
                return $this->sendError(__('responses.lab_program_not_found'), 404);
            }
            $deleteResourceModule = $this->resourceModuleRepository->delete($slug);
            if($deleteResourceModule) {
                return $this->sendResponse(null, __('responses.lab_program_delete'));
            }

            return $this->sendError(__('responses.lab_program_not_delete'), 400);
        }catch(\Exception $e){
            return $this->sendError(__('response.send_error'),500);
        }
    }

    public function deleteMedia($resource_module_id){
        try{
            $deleteResourceModule = $this->resourceModuleRepository->deleteMedia($resource_module_id);
            if($deleteResourceModule) {
                return $this->sendResponse(null, __('responses.lab_program_delete'));
            }
        }catch(\Exception $e){
            return $this->sendError(__('response.send_response'));
        }
    }

    public function checkName($title){
        try{
            $checkResourceModuleNameExistsOrNot=$this->resourceModuleRepository->checkName($title);
            if ($checkResourceModuleNameExistsOrNot) {
                return $this->sendError(__('responses.resource_module_not_available'));
            }

            return $this->sendResponse([], __('responses.resource_module_name_available'), 400);
        }catch(\Exception $e){
            return $this->sendError(__('response.send_error'));
        }
    }

    public function checkSlug($slug){
        try{
                $checkResourceModuleNameExistsOrNot=$this->resourceModuleRepository->checkSlug($slug);
                if ($checkResourceModuleNameExistsOrNot) {
                    return $this->sendError(__('responses.resource_module_not_available'));
                }
                return $this->sendResponse([], __('responses.resource_module_name_available'), 400);
            }catch(\Exception $e){
                return $this->sendError(__('response.send_error'));
            }
    }
}
