<?php

namespace App\Http\Controllers\Api\Manage\LabMarketplace;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\LabMarketPlace\LabMarketplaceRequest;
use App\Http\Resources\Manage\LabMarketplace\LabMarketplaceResource;
use App\Http\Resources\Manage\LabProgram\LabProgramResource;
use App\Repositories\Api\Manage\LabMarketplace\LabMarketplaceRepository;


class LabMarketplaceController extends AppBaseController
{
    private LabMarketplaceRepository $labMarketplaceRepository;

    public function __construct(LabMarketplaceRepository $labMarketplaceRepository)
    {
        $this->labMarketplaceRepository = $labMarketplaceRepository;
    }

    public function createLabMarketplace($slug,LabMarketplaceRequest $request){
        try {
            $checkLabExistsOrNot = $this->labMarketplaceRepository->getLabBasedOnSlug($slug);
            if (!$checkLabExistsOrNot) {
                return $this->sendError(__('responses.lab_slug_not_found'), 404);
            }
            $checkLabMarketplace = $this->labMarketplaceRepository->getCheckUuid($checkLabExistsOrNot->uuid);
            if ($checkLabMarketplace) {
                return $this->sendError(__('responses.already_cloned'), 200);
            }
            $getOrganizationId=$this->labMarketplaceRepository->getOrganizationIdBasedOnUuid($request->organization_id);
            if(!$getOrganizationId){
                return $this->sendError(__('responses.lab_slug_not_found'), 404);
            }
            $labMarketplace=$this->labMarketplaceRepository->createLabMarketplace($slug,$checkLabExistsOrNot->id,$getOrganizationId->id);
            if ($labMarketplace) {
                return $this->sendResponse($labMarketplace, __('responses.template_lab_stored_success'), 200);
            }
            return $this->sendError(__('responses.template_lab_stored_failed'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug){
        try {
            $labMarketplace = $this->labMarketplaceRepository->getLabMarketplaceBasedOnSlug($slug);
            if ($labMarketplace) {
                return $this->sendResponse(LabMarketplaceResource::make($labMarketplace), __('responses.found_lab_program_view'));
            }
            return $this->sendError(__('responses.not_found_lab_program_view'), 404);
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteLabMarketplace($slug){
        try{
            $checkLabMarketplaceExists=$this->labMarketplaceRepository->getLabMarketplaceBasedOnSlug($slug);
           if(!$checkLabMarketplaceExists){
               return $this->sendError(__('responses.lab_marketplace_not_exists'),404);
           }
            $deleteLabMarketplace=$this->labMarketplaceRepository->deleteLabMarketplace($slug,$checkLabMarketplaceExists->id);
            if($deleteLabMarketplace){
                return $this->sendResponse(null, __('responses.lab_marketplace_deleted_successfully'));
            }
            return $this->sendError(__('responses.lab_marketplace_deleted_failed'), 402);
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
