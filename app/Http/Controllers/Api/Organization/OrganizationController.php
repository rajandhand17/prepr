<?php

namespace App\Http\Controllers\Api\organization;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Repositories\Api\Organization\OrganizationRepository;
use App\Http\Resources\Organization\OrganizationResource;
use App\Http\Requests\Organization\CreateOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Http\Requests\Organization\DeleteOrganizationRequest;
use App\Http\Requests\Organization\ViewOrganizationRequest;

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
    
    /**
     * @OA\Get(
     *     path="/api/v1/organization/create-organization",
     *     tags={"Organization API - Create Organization"},
     *     summary="Create Organization with different parameters",
     *     description="Create Organization with different parameters",
     *     operationId="createOrganization",
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="User id for get the user",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Organization name for create the name",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         description="Organization description for describe the organization",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="profile_image",
     *         in="query",
     *         description="Organization profile Image for choose profile image",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="cover_image",
     *         in="query",
     *         description="Organization cover Image for choose cover image",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="website",
     *         in="query",
     *         description="Website for the organization",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="about",
     *         in="query",
     *         description="About for the organization",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Category values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="total_employees",
     *         in="query",
     *         description="Total Employees for the show total employees",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
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
    public function create(CreateOrganizationRequest $request)
    {   
        try {
            $organization=$this->organizationRepository->create($request);
            if ($organization) {
                return $this->sendResponse(null, __('responses.create_organization'));
            }
            return $this->sendError(__('responses.create_organization_failed'));
        } catch (\Exception $e) {
         return $this->sendError(__('responses.send_error'), 500);
        }
    }
   
    /**
     * @OA\Get(
     *     path="/api/v1/organization/update-organization",
     *     tags={"Organization API - Update Organization"},
     *     summary="Update Organization with different parameters",
     *     description="Update Organization with different parameters",
     *     operationId="updateOrganization",
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="User id for get the user",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="organization_id",
     *         in="query",
     *         description="User id for get the user",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Organization name for create the name",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         description="Organization description for describe the organization",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="profile_image",
     *         in="query",
     *         description="Organization profile Image for choose profile image",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="cover_image",
     *         in="query",
     *         description="Organization cover Image for choose cover image",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="website",
     *         in="query",
     *         description="Website for the organization",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="about",
     *         in="query",
     *         description="About for the organization",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Category values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="total_employees",
     *         in="query",
     *         description="Total Employees for the show total employees",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
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
    public function update(UpdateOrganizationRequest $request)
    {
        try {
            $organization=$this->organizationRepository->update($request);
            dd($organization);
            if ($organization) {
                return $this->sendResponse(null, __('responses.updated_organization'));
            }
            return $this->sendError(__('responses.updated_organization_failed'));
        } catch (\Exception $e) {
            
            return false;
        }
    }
   /**
     * @OA\Get(
     *     path="/api/v1/organization/delete-organization",
     *     tags={"Organization API - delete Organization"},
     *     summary="Delete Organization with different parameters",
     *     description="Delete Organization with different parameters",
     *     operationId="deleteOrganization",
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="organization_id",
     *         in="query",
     *         description="Organization id for get the user",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
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
    public function delete(DeleteOrganizationRequest $request)
    { 
        try {
            $organization=$this->organizationRepository->delete($request);
            dd($organization);
            if($organization==true){
                return $this->sendResponse(null,__('responses.delete_organizations_success'));
            }
            return $this->sendError(__('responses.delete_organizations_failed'));
        } catch (\Exception $e) {
           return $this->sendError(__('responses.send_error'), 500);
        }
    }

   public function view(ViewOrganizationRequest $request)
    {    
        try {
            $organization=$this->organizationRepository->view($request); 
            if($organization!==false){
                return $this->sendResponse($organization,__('responses.organization_view_get'));
            }
            return $this->sendError(__('responses.organization_view_get_failed'));
        } catch (\Exception $e) {
           return $this->sendError(__('responses.send_error'), 500);
        }
    }

}
