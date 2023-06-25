<?php
/**
 * @OA\Tag(
 *     name="MemberManagementController",
 *     description="Operations related to MemberManagementController"
 * )
 */

namespace App\Http\Controllers\Api\MemberManagement;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\MemberManagement\CreateMemberManagementRequest;
use App\Http\Requests\MemberManagement\DeleteMemberManagementRequest;
use App\Http\Resources\MemberManagement\MemberManagementResource;
use App\Http\Resources\Roles\RolesResource;
use App\Repositories\Api\MemberManagement\MemberManagementRepository;
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
            $member_mangement = $this->memberManagementRepository->getMembers($component, $slug, $request);
            if ($member_mangement['success'] == true && isset($member_mangement['data'])) {
                return $this->sendResponse(MemberManagementResource::collection($member_mangement['data']), __('responses.member_manager_found'));
            }

            return $this->sendError($member_mangement['message'], $member_mangement['code']);
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
                return $this->sendError(ucfrist($component).' Not Found', 403);
            }
            $memberLists = $this->memberManagementRepository->addMembers($checkComponentBasedOnSlug, $component, $request);

            if ($memberLists != false) {
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
            $member_mangement = $this->memberManagementRepository->deleteMembers($component, $slug, $request);
            if ($member_mangement) {
                return $this->sendResponse(null, __('responses.member_manager_delete'));
            }

            return $this->sendError(__('responses.member_manager_not_delete'), 400);
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
                if (!auth()->user()->isAbleTo('change_organization_ownership')) {
                    $getRoles = $getRoles->reject(function ($role) {
                        return $role->display_name == config('constants.role_name.organization_owner');
                    });
                }

                return $this->sendResponse(RolesResource::collection($getRoles), 'Roles fetched successfully');
            }

            return $this->sendError('Roles not found', 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
