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

    public function index($component, $slug, Request $request)
    {
        try {
            $checkComponentBasedOnSlug = UtilityHelper::checkComponentSlugExistOrNot($component, $slug);

            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(ucfirst($component).' '.__('responses.not_found_required'), 404);
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
            UtilityHelper::logError($e);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create($component, $slug, CreateMemberManagementRequest $request)
    {
        try {
            $checkComponentBasedOnSlug = UtilityHelper::checkComponentSlugExistOrNot($component, $slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(ucfirst($component).' '.__('responses.not_found_required'), 404);
            }
            if ($component != 'organization' && $request->role != 'User') {
                return $this->sendError(__('responses.select_valid_role_error'), 422);
            }
            $memberLists = $this->memberManagementRepository->addMembers($checkComponentBasedOnSlug, $component, $request);
            if ((count($memberLists['invalid_emails']) > 0 || count($memberLists['already_members']) > 0) && count($memberLists['invited_emails']) < 1) {
                return $this->sendError($memberLists['add_member_response'], 422);
            } elseif ($memberLists) {
                return $this->sendResponse($memberLists, $memberLists['add_member_response']);
            }

            return $this->sendError(__('responses.create_member_manger_failed'), 404);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function delete($component, $slug, DeleteMemberManagementRequest $request)
    {
        try {
            $checkComponentBasedOnSlug = UtilityHelper::checkComponentSlugExistOrNot($component, $slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(ucfirst($component).' '.__('responses.not_found_required'), 404);
            }
            $member_management = $this->memberManagementRepository->deleteMembers($checkComponentBasedOnSlug, $component, $request);
            if ($member_management) {
                return $this->sendResponse(null, __('responses.member_manger_delete'));
            }

            return $this->sendError(__('responses.member_manger_not_delete'), 400);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function acceptOrRejectLabJoinRequest(Request $request, $component, $slug, $action)
    {
        try {
            $checkComponentBasedOnSlug = UtilityHelper::checkComponentSlugExistOrNot($component, $slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(ucfirst($component).' '.__('responses.not_found_required'), 404);
            }
            $checkLabStatus = $this->memberManagementRepository->checkLabJoinUnjoinStatus($request, $checkComponentBasedOnSlug, $component);
            if ($checkLabStatus) {
                $member_management = $this->memberManagementRepository->acceptOrRejectLabJoinRequest($request, $checkComponentBasedOnSlug, $component, $action);
                if ($member_management) {
                    return $this->sendResponse(null, __('responses.join_request_'.$action.'_successfully'));
                }

                return $this->sendError(__('responses.join_request_'.$action.'_failed'), 400);
            }

            return $this->sendError(__('responses.request_not_exist'), 400);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function downloadSample()
    {
        try {
            return $this->memberManagementRepository->downloadSample();
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
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
            UtilityHelper::logError($e);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function changeRole($component, ChangeRoleRequest $request)
    {
        try {
            $changeRoleResponse = $this->memberManagementRepository->changeRole($request, $component);
            if ($changeRoleResponse) {
                return $this->sendResponse([], __('responses.role_assigned_successfully'));
            }

            return $this->sendError(__('responses.role_assigned_failed'), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
