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
     *     path="/api/v1/organization/{language}/{slug}",
     *     tags={"Organization API - Organization List"},
     *     summary="Finds lists of Organization List",
     *     description="Get all the Organization List",
     *     security={{"bearerAuth":{}}},
     *     operationId="view",
     *     @OA\Parameter(
     *         name="language",
     *         in="path",
     *         required=true,
     *         description="slug of the user to retrieve data",
     *        
     *     ),
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="slug of the user to retrieve data",
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
    public function view($slug,Request $request)
    {   
       try {
          $organization=$this->organizationRepository->view($slug,$request->language);
          
          if($organization==="not_exists"){
            return $this->sendError(__('responses.organization_not_exists'),404);
        }
          if ($organization) {
            return $this->sendResponse(OrganizationResource::collection($organization), __('responses.found_organizations_list'));
          
          }
        return $this->sendError(__('responses.found_not_organizations_list'),404);
         
       }catch (\Exception $e){ 
         return $this->sendError(__('responses.send_error'), 500);
     }
    }
    
    /**
     * @OA\Post(
     *     path="/api/v1/organization/create",
     *     tags={"Organization API - Create Organization"},
     *     summary="Create Organization with different parameters",
     *     description="Create Organization with different parameters",
     *     operationId="create",
     *     security={ {"bearer": {} }},
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
     *         required=false,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="profile_image",
     *         in="query",
     *         description="Organization profile Image for choose profile image",
     *         required=false,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="cover_image",
     *         in="query",
     *         description="Organization cover Image for choose cover image",
     *         required=false,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="website",
     *         in="query",
     *         description="Website for the organization",
     *         required=false,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="about",
     *         in="query",
     *         description="About for the organization",
     *         required=false,
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
     *         description="Total Employees for the show total employees!",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="magnet_community_id",
     *         in="query",
     *         description="Magnet community id for the magnet community id!",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="latitude",
     *         in="query",
     *         description="Latitude for the magnet community id!",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="longitude",
     *         in="query",
     *         description="Longitude for the organization location!",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="address",
     *         in="query",
     *         description="address for the organization!",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="city of the organization!",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="state",
     *         in="query",
     *         description="state of the organization!",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="country",
     *         in="query",
     *         description="country of the organization!",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="zip_code",
     *         in="query",
     *         description="zip_code of the organization!",
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
            if($organization['success']==true) {
                return $this->sendResponse($organization['data'],$organization['message']);
            }else{
                return $this->sendError($organization['message'],422);
            }
        } catch (\Exception $e) {
         return $this->sendError(__('responses.send_error'), 500);
        }
    }
   
    /**
     * @OA\Put(
     *     path="/api/v1/organization/update/{language}/{slug}",
     *     tags={"Organization API - Update Organization"},
     *     summary="Update Organization with different parameters",
     *     description="Update Organization with different parameters",
     *     operationId="update",
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *         name="language",
     *         in="path",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Organization slug for create the slug",
     *         required=false,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Organization name for create the name",
     *         required=false,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         description="Organization description for describe the organization",
     *         required=false,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="profile_image",
     *         in="query",
     *         description="Organization profile Image for choose profile image",
     *         required=false,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="cover_image",
     *         in="query",
     *         description="Organization cover Image for choose cover image",
     *         required=false,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="website",
     *         in="query",
     *         description="Website for the organization",
     *         required=false,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="about",
     *         in="query",
     *         description="About for the organization",
     *         required=false,
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
     *     @OA\Parameter(
     *         name="latitude",
     *         in="query",
     *         description="latitude for organization location latitude",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="longitude",
     *         in="query",
     *         description="longitude for organization location longitude",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="address",
     *         in="query",
     *         description="Address for organization location address",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="City for organizations city name",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="state",
     *         in="query",
     *         description="State for organizations state name",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="country",
     *         in="query",
     *         description="Country for organizations country name",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="zipcode",
     *         in="query",
     *         description="Zip-code for organizations zipcode!",
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
    public function update($slug,UpdateOrganizationRequest $request)
    {   
        try {
            $organization=$this->organizationRepository->update($slug,$request);
            if($organization['success']==true){
                return $this->sendResponse(null,$organization['message']);
            }else{
                return $this->sendError($organization['message'],204);
            }
            return $this->sendError(__('responses.updated_organization_failed'));
        }catch (\Exception $e){
            return false;
        }
    }
   /**
     * @OA\Delete(
     *     path="/api/v1/organization/delete",
     *     tags={"Organization API - Delete Organization"},
     *     summary="Delete Organization with different parameters",
     *     security={ {"bearer": {} }},
     *     description="Delete Organization with different parameters",
     *     operationId="delete",
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="slug",
     *         in="query",
     *         description="Slug for get the user",
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

    public function delete($slug,Request $request)
    {   
        try {
            $organization=$this->organizationRepository->delete($slug,$request->language);
            if($organization==="not_exists"){
                return $this->sendError(__('responses.organization_not_exists'),404);
            }
            if($organization===true){
                return $this->sendResponse(null,__('responses.delete_organizations_success'));
            }
            
            return $this->sendError(__('responses.delete_organizations_failed'),400);
        } catch (\Exception $e) {
           return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/organization/view",
     *     tags={"Organization API - View Organization"},
     *     summary="View Organization with different parameters",
     *     description="View Organization with different parameters",
     *     operationId="list",
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *      ),
     *     @OA\Parameter(
     *         name="slug",
     *         in="query",
     *         description="Slug for get the user",
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
   public function list(Request $request)
   {    
       try { 
           $organization=$this->organizationRepository->list($request->language);
           if($organization!==false){
               return $this->sendResponse($organization,__('responses.organization_view_get'));
           }
           return $this->sendError(__('responses.organization_view_get_failed'),400);
       } catch (\Exception $e) {
          return $this->sendError(__('responses.send_error'), 500);
       }
   }

}
