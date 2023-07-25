<?php
/**
 * @OA\Tag(
 *     name="MemberManagementController",
 *     description="Operations related to MemberManagementController"
 * )
 */

namespace App\Http\Controllers\Api\Manage\MemberManagement;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\MemberManagement\ChangeRoleRequest;
use App\Http\Requests\Manage\MemberManagement\CreateMemberManagementRequest;
use App\Http\Requests\Manage\MemberManagement\DeleteMemberManagementRequest;
use App\Http\Resources\EmailTemplate\EmailTemplateResource;
use App\Http\Resources\Manage\MemberManagement\MemberManagementResource;
use App\Http\Resources\Manage\Roles\RolesResource;
use App\Repositories\Api\Manage\MemberManagement\MemberManagementRepository;
use App\Services\UserService;
use Illuminate\Http\Request;

class MemberManagementController extends AppBaseController
{
    private $memberManagementRepository;

    public function __construct(MemberManagementRepository $memberManagementRepository)
    {
        $this->memberManagementRepository = $memberManagementRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/member-management/{component}/{slug}?language=en",
     *     tags={"Member Management API -  List"},
     *     summary="Finds lists of Member Management",
     *     description="Get all the Member Management",
     *     security={{"bearerAuth":{}}},
     *     operationId="index",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="path",
     *         required=true,
     *         description="language define the choosen language",
     *
     *     ),
     *     @OA\Parameter(
     *         name="component",
     *         in="path",
     *         required=true,
     *         description="component define type",
     *
     *     ),
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="slug define the organization slug",
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
    public function index($component, $slug, Request $request)
    {
        try {
            $checkComponentBasedOnSlug = UtilityHelper::checkComponentSlugExistOrNot($component, $slug);

            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(ucfirst($component).' '.__('responses.not_found_required'), 403);
            }
            $memberManagementListing = $this->memberManagementRepository->getMembers($checkComponentBasedOnSlug, $component, $request);
            $getTemplate = $this->memberManagementRepository->getTemplate($request, $component);

            if ($getTemplate) {
                $user_name = UserService::joinName(auth()->user()->first_name, auth()->user()->last_name);
                $getTemplate->body_content = str_replace('user_name', $user_name, str_replace('component_title', $checkComponentBasedOnSlug->title, $getTemplate->body_content));
            }
            $response = [
                'id'                          => $checkComponentBasedOnSlug->uuid,
                'title'                       => $checkComponentBasedOnSlug->title,
                'slug'                        => $checkComponentBasedOnSlug->slug,
                'invitation_email'            => EmailTemplateResource::make($getTemplate),
            ];
            if ($memberManagementListing) {
                $response['total_user_count'] = $memberManagementListing->total();
                $response['per_page'] = $memberManagementListing->perPage();
                $response['count'] = $memberManagementListing->count();
                $response['current_page'] = $memberManagementListing->currentPage();
                $response['total_pages'] = $memberManagementListing->lastPage();
                $response['users'] = MemberManagementResource::collection($memberManagementListing);
            } else {
                $response['total_user_count'] = 0;
                $response['per_page'] = 0;
                $response['count'] = 0;
                $response['current_page'] = 1;
                $response['total_pages'] = 1;
                $response['users'] = [];

                return $this->sendResponse($response, __('responses.create_member_manger_failed'));
            }

            return $this->sendResponse($response, __('responses.member_manager_found'));
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/member-management/{component}/{slug}/create?language=en",
     *     tags={"Member Management API -  create"},
     *     summary="Send request for create member management",
     *     operationId="creates",
     *
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Enter type for member management!",
     *         required=true,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="invite_type",
     *         in="query",
     *         description="Enter invite-type for member management!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="Enter role for member management!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="module_id",
     *         in="query",
     *         description="Enter module_id for member management!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="inviter_id",
     *         in="query",
     *         description="Enter inviter id for member management!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="subject_line",
     *         in="query",
     *         description="Enter subject line for member management!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="email_body",
     *         in="query",
     *         description="Enter email body for member management!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="invite_status",
     *         in="query",
     *         description="Enter invite status for member management!",
     *         required=true,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="invite_email",
     *         in="query",
     *         description="Enter invite email for member management!",
     *         required=true,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="inviter_id",
     *         in="query",
     *         description="Enter inviter id for member management!",
     *         required=true,
     *         explode=true,
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
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function create($component, $slug, CreateMemberManagementRequest $request)
    {
        try {
            $checkComponentBasedOnSlug = UtilityHelper::checkComponentSlugExistOrNot($component, $slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(ucfirst($component).' '.__('responses.not_found_required'), 403);
            }
            if ($component != 'organization' && $request->role != 'User') {
                return $this->sendError(__('responses.select_valid_role_error'), 403);
            }
            $memberLists = $this->memberManagementRepository->addMembers($checkComponentBasedOnSlug, $component, $request);
            if ($memberLists) {
                return $this->sendResponse($memberLists, __('responses.create_member_manger_success'));
            }

            return $this->sendError(__('responses.create_member_manger_failed'), 403);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/member-management/{component}/{slug}/delete?language=en",
     *     tags={"Member Management API - delete"},
     *     summary="Member management apis delete",
     *     operationId="deletes",
     *
     *     @OA\Parameter(
     *         name="id[]",
     *         in="query",
     *         description="Member management delete id",
     *         required=true,
     *         explode=true,
     *
     *         @OA\Schema(type="array", @OA\Items(type="integer")),
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
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function delete($component, $slug, DeleteMemberManagementRequest $request)
    {
        try {
            $checkComponentBasedOnSlug = UtilityHelper::checkComponentSlugExistOrNot($component, $slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(ucfirst($component).' '.__('responses.not_found_required'), 403);
            }
            $member_management = $this->memberManagementRepository->deleteMembers($checkComponentBasedOnSlug, $component, $request);
            if ($member_management) {
                return $this->sendResponse(null, __('responses.member_manger_delete'));
            }

            return $this->sendError(__('responses.member_manger_not_delete'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function downloadSample()
    {
        try {
            return $this->memberManagementRepository->downloadSample();
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getRoles()
    {
        try {
            $getRoles = $this->memberManagementRepository->getRoles(config('constants.role_type.external'));
            if ($getRoles) {
                $getRoles = $getRoles->reject(function ($role) {
                    return $role->display_name == config('constants.role_name.organization_owner');
                });

                return $this->sendResponse(RolesResource::collection($getRoles), __('responses.found_role_list'));
            }

            return $this->sendError(__('responses.not_found_role_list'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function changeRole($component, ChangeRoleRequest $request)
    {
        try {
            $changeRoleResponse = $this->memberManagementRepository->changeRole($request, $component);
            if ($changeRoleResponse) {
                return $this->sendResponse([], __('responses.role_assigned_sucessfully'));
            }

            return $this->sendError(__('responses.role_assigned_failed'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
