<?php

namespace App\Http\Controllers\Api\organization;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Organization\CreateOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Http\Resources\Organization\OrganizationResource;
use App\Jobs\subscribePlanJob;
use App\Repositories\Api\Organization\OrganizationRepository;
use App\Services\OrganizationAddressService;
use App\Services\OrganizationMemberService;
use App\Services\OrganizationService;
use Illuminate\Http\Request;

class OrganizationController extends AppBaseController
{
    private $organizationRepository;

    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/organization/",
     *     tags={"Organization API - Organization List"},
     *     summary="Finds lists of Organization List",
     *     description="Get all the Organization List",
     *     security={{"bearerAuth":{}}},
     *     operationId="view",
     *
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
     *
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
    public function view(Request $request, OrganizationService $organizationService, $slug)
    {
        try {
            $organization = $this->organizationRepository->view($request, $organizationService, $slug);
            if ($organization === 'not_exists') {
                return $this->sendError(__('responses.organization_not_exists'), 404);
            }
            if ($organization) {
                return $this->sendResponse(OrganizationResource::collection($organization), __('responses.found_organizations_list'));
            }

            return $this->sendError(__('responses.found_not_organizations_list'), 404);
        } catch (\Exception $e) {
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
     *
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
     *
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
    public function create(CreateOrganizationRequest $request, OrganizationService $organizationService, OrganizationAddressService $organizationAddresss, OrganizationMemberService $organizationMember)
    {
        try {
            $profile_image_path = null;
            $cover_image_path = null;
            $checkOrganization = $this->organizationRepository->checkOrganizationExist($request, $organizationService, $organizationAddresss, $organizationMember);
            if (!$checkOrganization) {
                return $this->sendError(__('responses.organization_name_unique'), 422);
            }
            $checkOrganizationInTrash = $this->organizationRepository->checkOrganizationExistInTrash($request, $organizationService, $organizationAddresss, $organizationMember);
            if (!$checkOrganizationInTrash) {
                return $this->sendError(__('responses.trashed_records'), 422);
            }

            if ($request->profile_image !== null) {
                $upload_profile_image = $this->organizationRepository->uploadOrganizationProfileImage($request, $organizationService, $organizationAddresss, $organizationMember);
                if ($upload_profile_image == false) {
                    return $this->sendError(__('responses.fail_organization_image_upload'), 400);
                }
                $profile_image_path = $upload_profile_image;
            }
            if ($request->cover_image !== null) {
                $upload_cover_image = $this->organizationRepository->uploadOrganizationCoverImage($request, $organizationService, $organizationAddresss, $organizationMember);
                if ($upload_cover_image == false) {
                    return $this->sendError(__('responses.fail_organization_image_upload'), 500);
                }
                $cover_image_path = $upload_cover_image;
            }
            $organization = $this->organizationRepository->createOrganization($request, $organizationService, $organizationAddresss, $organizationMember, $profile_image_path, $cover_image_path);
            if ($organization) {
                if (isset($request->organization_address) && !empty($request->organization_address)) {
                    $organization_addresss = $this->organizationRepository->createOrganizationAddress($request, $organizationService, $organizationAddresss, $organizationMember, $profile_image_path, $cover_image_path, $organization->id);
                }
                if (isset($request->organization_members) && !empty($request->organization_members)) {
                    $organization_member = $this->organizationRepository->organizationAddMemeber($request, $organizationService, $organizationAddresss, $organizationMember, $profile_image_path, $cover_image_path, $organization->id);
                }
                $details['cust_id'] = auth()->user()->id;
                $details['organization_id'] = $organization->id;
                $details['plan'] = config('chargebee.base_plan');
                dispatch(new subscribePlanJob($details));

                return $this->sendResponse($organization, __('responses.create_organization'));
            } else {
                return $this->sendError($organization['message'], 409);
            }
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/organization/{slug}/update",
     *     tags={"Organization API - Update Organization"},
     *     summary="Update Organization with different parameters",
     *     description="Update Organization with different parameters",
     *     operationId="update",
     *     security={ {"bearer": {} }},
     *
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
     *      .   required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="zipcode",
     *         in="query",
     *         description="Zip-code for organizations zipcode!",
     *         required=false,
     *         explode=true,
     *     ),
     *
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
    public function update($slug, UpdateOrganizationRequest $request, OrganizationService $organizationService, OrganizationAddressService $organizationaddresss)
    {
        try {
            $profile_images_path = null;
            $exists_slug=$this->organizationRepository->checkSlug($slug,$request,$organizationService,$organizationaddresss);
            if(!$exists_slug){
                $response = ['success' => false, 'message' => __('responses.organization_slug_not_exists')];

                return $response;
            }
            if ($request->profile_image !== null) {
                $profile_image_path = $this->organizationRepository->updateOrganizationProfileImage($request, $organizationService, $organizationaddresss);
                if ($profile_image_path == false) {
                    return $this->sendError(__('responses.fail_organization_image_upload'), 400);
                }
                $profile_images_path = $profile_image_path;
            }
            $cover_images_path = null;
            if ($request->cover_image !== null) {
                $cover_images_path = $this->organizationRepository->updateOrganizationCoverImage($request, $organizationService, $organizationaddresss);
            }
            $organization = $this->organizationRepository->updateOrganization($request, $organizationService, $cover_images_path, $profile_images_path, $slug);

            if (!empty($request->organization_address)) {
                $organization_address = $this->organizationRepository->updatesOrganizationAddress($request->organization_address, $organizationaddresss, $organization->id);
            }
            if ($organization) {
                return $this->sendResponse($organization, __('responses.updated_organization'));
            }

            return $this->sendError(__('responses.updated_organization_failed'), 409);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/organization/{slug}/delete",
     *     tags={"Organization API - Delete Organization"},
     *     summary="Delete Organization with different parameters",
     *     security={ {"bearer": {} }},
     *     description="Delete Organization with different parameters",
     *     operationId="delete",
     *
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
     *
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
    public function delete($slug, Request $request, OrganizationService $organizationService)
    {
        try {
            $organization = $this->organizationRepository->delete($slug, $organizationService, $request->language);
            if ($organization === 'not_exists') {
                return $this->sendError(__('responses.organization_not_exists'), 404);
            }
            if ($organization === true) {
                return $this->sendResponse(null, __('responses.delete_organizations_success'));
            }

            return $this->sendError(__('responses.delete_organizations_failed'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

   /**
    * @OA\Get(
    *     path="/api/v1/organization/{slug}/view",
    *     tags={"Organization API - View Organization"},
    *     summary="View Organization with different parameters",
    *     description="View Organization with different parameters",
    *     operationId="list",
    *
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
    *
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
    public function list(Request $request,OrganizationService $organizationService)
    {
        try {
            $organization = $this->organizationRepository->list($organizationService, $request->language);
            if ($organization !== false) {
                return $this->sendResponse($organization, __('responses.organization_view_get'));
            }

            return $this->sendError(__('responses.organization_view_get_failed'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
