<?php

namespace App\Http\Controllers\Api\organization;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Repositories\Api\Organization\OrganizationRepository;
use App\Http\Resources\Organization\OrganizationResource;
use App\Http\Requests\Organization\CreateOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Http\Requests\Organization\DeleteOrganizationRequest;

class OrganizationController extends AppBaseController
{   
    private $organizationRepository;
    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }
    
    /**
     * @OA\Get(
     *     path="/api/v1/organization/organization-list",
     *     tags={"Organization API - Organization List"},
     *     summary="Finds lists of Organization List",
     *     description="Get all the Organization List",
     *     operationId="getOrganization",
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error!",
     *
     *     ),
     * )
     */
    public function getOrganization(Request $request)
    {  
       try {
          $organization=$this->organizationRepository->getOrganization($request);
          if ($organization) {
            return $this->sendResponse(OrganizationResource::collection($organization), __('responses.found_organizations_list'));
          }
        return $this->sendError(__('responses.found_not_organizations_list'));
         
       } catch (\Exception $e) {
         return $this->sendError(__('responses.send_error'), 500);
     }
    }

    public function createOrganization(CreateOrganizationRequest $request)
    {   
        try {
            $organization=$this->organizationRepository->createOrganization($request);
            if ($organization) {
                return $this->sendResponse(null, __('responses.create_organization'));
            }
            return $this->sendError(__('responses.create_organization_failed'));
        } catch (\Exception $e) {
            
         return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function updateOrganization(UpdateOrganizationRequest $request)
    {
        try {
            $organization=$this->organizationRepository->updateOrganization($request);
            if ($organization) {
                return $this->sendResponse(null, __('responses.updated_organization'));
            }
            return $this->sendError(__('responses.updated_organization_failed'));
        } catch (\Exception $e) {
            
            return false;
        }
    }

    public function deleteOrganization(DeleteOrganizationRequest $request)
    { 
        try {
            $organization=$this->organizationRepository->deleteOrganization($request);
            
            if($organization==true){
                return $this->sendResponse(null,__('responses.delete_organizations_success'));
            }
            return $this->sendError(__('responses.delete_organizations_failed'));
        } catch (\Exception $e) {
           return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
