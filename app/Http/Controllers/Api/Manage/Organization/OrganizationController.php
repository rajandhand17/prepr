<?php

namespace App\Http\Controllers\Api\Manage\Organization;

use App\Helpers\MixpanelHelper;
use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\Organization\CreateOrganizationRequest;
use App\Http\Requests\Manage\Organization\UpdateOrganizationCustomizationRequest;
use App\Http\Requests\Manage\Organization\UpdateOrganizationRequest;
use App\Http\Resources\Manage\Organization\OrganizationChargebeeLimitResource;
use App\Http\Resources\Manage\Organization\OrganizationDetailResource;
use App\Http\Resources\Manage\Organization\OrganizationResource;
use App\Repositories\Api\Manage\Organization\OrganizationRepository;
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
    public function index(Request $request)
    {
        try {
            if (!auth()->user()->isAbleTo('view_organization')) {
                return $this->sendError(__('responses.permission_forbidden'), 403);
            }
            if (!in_array($request->owner, ['all', 'my', 'invited'])) {
                return $this->sendError(__('responses.owner_required'), 403);
            }
            $organization = $this->organizationRepository->getOrganizationList($request);
            if ($organization !== false) {
                $response = [
                    'total_count'  => $organization->total(),
                    'per_page'     => $organization->perPage(),
                    'count'        => $organization->count(),
                    'current_page' => $organization->currentPage(),
                    'total_pages'  => $organization->lastPage(),
                    'list'         => OrganizationResource::collection($organization),
                ];

                return $this->sendResponse($response, __('responses.found_organization_list'));
            }

            return $this->sendError(__('responses.not_found_organization_list'), 400);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
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
    public function show($slug)
    {
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if (!auth()->user()->isAbleTo('view_organization', $organization)) {
                return $this->sendError(__('responses.permission_forbidden'), 403);
            }
            if ($organization) {
                MixpanelHelper::mixpanel_tracking(config('mixpanel.view_lab'), $organization, auth()->user(), request()->ip());

                return $this->sendResponse(OrganizationDetailResource::make($organization), __('responses.found_organization_list'));
            }

            return $this->sendError(__('responses.organization_not_exists'), 404);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

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
    public function create(CreateOrganizationRequest $request)
    {
        try {
            if (!auth()->user()->isAbleTo('create_organization')) {
                return $this->sendError(__('responses.permission_forbidden'), 403);
            }
            $profile_image_path = config('site-settings.default_organization_profile_image');
            $cover_image_path = config('site-settings.default_organization_cover_image');
            $checkOrganization = $this->organizationRepository->checkOrganizationExistBasedOnTitle($request);
            if (!$checkOrganization) {
                return $this->sendError(__('responses.organization_title_unique'), 422);
            }
            $checkOrganizationInTrash = $this->organizationRepository->checkOrganizationExistInTrashBasedOnTitle($request);
            if (!$checkOrganizationInTrash) {
                return $this->sendError(__('responses.trashed_records'), 422);
            }
            if ($request->profile_image !== null) {
                $upload_profile_image = $this->organizationRepository->uploadOrganizationProfileImage($request);
                if (!$upload_profile_image) {
                    return $this->sendError(__('responses.image_upload_failed_profile_image'), 400);
                }
                $profile_image_path = $upload_profile_image;
            }
            if ($request->cover_image !== null) {
                $upload_cover_image = $this->organizationRepository->uploadOrganizationCoverImage($request);
                if (!$upload_cover_image) {
                    return $this->sendError(__('responses.image_upload_failed_cover_image'), 500);
                }
                $cover_image_path = $upload_cover_image;
            }
            $organization = $this->organizationRepository->createOrganization($request, $profile_image_path, $cover_image_path);

            if ($organization) {
                if ($request->has('organization_address') && !empty($request->organization_address)) {
                    $this->organizationRepository->createOrganizationAddress($request, $organization->id);
                }
                if ($request->has('organization_members') && !empty($request->organization_members)) {
                    $this->organizationRepository->createOrganizationMembers($request, $organization->id);
                }
                if ($request->has('external_links') && !empty($request->external_links)) {
                    $this->organizationRepository->createOrganizationExternalLinks($request, $organization->id);
                }
                $selectPlan = $this->organizationRepository->selectPlan($organization, $request);

                return $this->sendResponse(OrganizationResource::make($organization), __('responses.organization_stored_success'));
            } else {
                return $this->sendError(__('responses.organization_stored_failed'), 409);
            }
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

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
    public function update($slug, UpdateOrganizationRequest $request)
    {
        try {
            $checkOrganization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if (!$checkOrganization) {
                return $this->sendError(__('responses.organization_not_found'), 404);
            }
            if (!auth()->user()->isAbleTo('edit_organization', $checkOrganization)) {
                return $this->sendError(__('responses.organization_update_access_denied'), 403);
            }
            $profile_images_path = str_replace(config('site-settings.aws_url'), '', $checkOrganization->profile_image);
            $cover_images_path = str_replace(config('site-settings.aws_url'), '', $checkOrganization->cover_image);
            if ($request->profile_image !== null) {
                $profile_image_path = $this->organizationRepository->uploadOrganizationProfileImage($request);
                if ($profile_image_path == false) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $profile_images_path = $profile_image_path;
            }

            if ($request->cover_image !== null) {
                $cover_image_path = $this->organizationRepository->uploadOrganizationCoverImage($request);
                if ($cover_image_path == false) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $cover_images_path = $cover_image_path;
            }

            $organization = $this->organizationRepository->updateOrganization($request, $cover_images_path, $profile_images_path, $slug);

            if ($organization) {
                if ($request->has('organization_address') && !empty($request->organization_address)) {
                    $this->organizationRepository->updatesOrganizationAddress($request, $organization->id);
                }
                if ($request->has('organization_members') && !empty($request->organization_members)) {
                    $this->organizationRepository->updatesOrganizationMembers($request, $organization->id);
                }
                if ($request->has('external_links') && !empty($request->external_links)) {
                    $this->organizationRepository->updateOrganizationExternalLinks($request, $organization->id);
                }

                return $this->sendResponse(OrganizationResource::make($organization), __('responses.organization_update_successfully'), 200);
            }

            return $this->sendError(__('responses.organization_not_update'), 409);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

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
    public function delete($slug, Request $request)
    {
        try {
            $checkOrganization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if (!$checkOrganization) {
                return $this->sendError(__('responses.organization_not_exists'), 422);
            }
            if (!auth()->user()->isAbleTo('delete_organization', $checkOrganization)) {
                return $this->sendError(__('responses.organization_delete_access_denied'), 403);
            }
            $deleteOrganization = $this->organizationRepository->deleteOrganization($checkOrganization, $request);
            if ($deleteOrganization) {
                return $this->sendResponse(null, __('responses.organization_delete'), 200);
            }

            return $this->sendError(__('responses.organization_not_delete'), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkSlug($slug)
    {
        try {
            $organization = $this->organizationRepository->checkSlug($slug);
            if (!$organization) {
                return $this->sendResponse([], __('responses.lab_slug_available'), 200);
            }

            return $this->sendError(__('responses.already_exists'), 400);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getOrganizationList(Request $request)
    {
        try {
            if (!auth()->user()->hasRole([
                'organization_owner', 'organization_manager', 'lab_manager', 'challenge_manager', 'resource_manager',
            ])) {
                return $this->sendError(__('responses.organization_delete_access_denied'), 403);
            }
            $organization = $this->organizationRepository->getOrganizationListOnlyNameAndUuid($request);
            if ($organization !== false) {
                return $this->sendResponse($organization, __('responses.found_organization_list'));
            }

            return $this->sendError(__('responses.not_found_organization_list'), 400);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function subscriptionDetails($slug)
    {
        try {
            $organizationDetail = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if (!$organizationDetail) {
                return $this->sendError(__('responses.organization_not_exists'), 422);
            }

            $planData = $this->organizationRepository->planData($organizationDetail);
            if ($planData) {
                return $this->sendResponse(OrganizationChargebeeLimitResource::make($organizationDetail), __('responses.plan_details_retrived'));
            }

            return $this->sendError(__('responses.plan_not_retrived'), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function organizationCustomization($slug, UpdateOrganizationCustomizationRequest $request)
    {
        try {
            $checkOrganization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if (!$checkOrganization) {
                return $this->sendError(__('responses.organization_not_found'), 404);
            }
            if (!auth()->user()->isAbleTo('edit_organization', $checkOrganization)) {
                return $this->sendError(__('responses.organization_update_access_denied'), 403);
            }
            if ($request->has('enable_custom_login_and_registration') && !empty($request->enable_custom_login_and_registration)) {
                $updateOrganizationCustomLoginRegistration = $this->organizationRepository->updateOrganizationCustomLoginRegistration($request, $checkOrganization);
                if ($updateOrganizationCustomLoginRegistration) {
                    return $this->sendResponse(OrganizationResource::make($checkOrganization), __('responses.organization_customization_update_successfully'), 200);
                }
            }

            return $this->sendError(__('responses.organization_customization_not_update'), 409);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
