<?php

namespace App\Http\Controllers\Api\ProjectMemberManagement;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\ProjectMemberManagement\CreateProjectMemberManagementRequest;
use App\Http\Requests\ProjectMemberManagement\DeleteProjectMemberManagementRequest;
use App\Http\Resources\EmailTemplate\EmailTemplateResource;
use App\Http\Resources\ProjectMemberManagement\ProjectAccessLevelResource;
use App\Http\Resources\ProjectMemberManagement\ProjectMemberManagementResource;
use App\Repositories\Api\Project\ProjectRepository;
use App\Repositories\Api\ProjectMemberManagement\ProjectMemberManagementRepository;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;

class ProjectMemberManagementController extends AppBaseController
{
    private $projectMemberManagementRepository;
    private $projectRepository;

    public function __construct(ProjectMemberManagementRepository $projectMemberManagementRepository, ProjectRepository $projectRepository)
    {
        $this->projectMemberManagementRepository = $projectMemberManagementRepository;
        $this->projectRepository = $projectRepository;
    }

    public function getRoles()
    {
        try {
            $getRoles = $this->projectMemberManagementRepository->getRoles();
            if ($getRoles) {
                return $this->sendResponse(ProjectAccessLevelResource::collection($getRoles), __('responses.found_role_list'));
            }

            return $this->sendError(__('responses.not_found_role_list'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function index($slug, Request $request)
    {
        try {
            $checkProjectExistsOrNot = UtilityHelper::checkComponentSlugExistOrNot('project', $slug);
            if ($checkProjectExistsOrNot == false) {
                return $this->sendError(__('responses.project_not_found'), 404);
            }

            if ($request->role) {
                if (!in_array($request->role, ['Team Leader', 'Editor', 'Viewer'])) {
                    return $this->sendError(__('responses.access_not_exists'), 422);
                }
            }

            $projectMemberManagementListing = $this->projectMemberManagementRepository->getProjectBasedParticipants($checkProjectExistsOrNot, $request);
            $getTemplate = $this->projectMemberManagementRepository->getTemplate($request->language);

            if ($getTemplate) {
                $user_name = UserService::joinName(auth()->user()->first_name, auth()->user()->last_name);
                $getTemplate->body_content = str_replace('user_name', $user_name, str_replace('component_title', $checkProjectExistsOrNot->title, $getTemplate->body_content));
            }
            if ($checkProjectExistsOrNot->is_submitted == '0') {
                $project_status = 'In Progress';
            } elseif ($checkProjectExistsOrNot->is_submitted == '1') {
                $project_status = 'Submitted';
            } else {
                $project_status = 'Late Submitted';
            }
            $response = [
                'id'                          => $checkProjectExistsOrNot->uuid,
                'title'                       => $checkProjectExistsOrNot->title,
                'status'                      => $project_status,
                'slug'                        => $checkProjectExistsOrNot->slug,
                'invitation_email'            => EmailTemplateResource::make($getTemplate),
            ];

            if ($projectMemberManagementListing) {
                $response['total_user_count'] = $projectMemberManagementListing->total();
                $response['per_page'] = $projectMemberManagementListing->perPage();
                $response['count'] = $projectMemberManagementListing->count();
                $response['current_page'] = $projectMemberManagementListing->currentPage();
                $response['total_pages'] = $projectMemberManagementListing->lastPage();
                $response['users'] = ProjectMemberManagementResource::collection($projectMemberManagementListing);
            } else {
                $response['total_user_count'] = 0;
                $response['per_page'] = 0;
                $response['count'] = 0;
                $response['current_page'] = 1;
                $response['total_pages'] = 1;
                $response['users'] = [];

                return $this->sendResponse($response, __('responses.participant_list_not_found'));
            }

            return $this->sendResponse($response, __('responses.participant_list_found'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function downloadSample()
    {
        try {
            return $this->projectMemberManagementRepository->downloadSample();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create($slug, CreateProjectMemberManagementRequest $request)
    {
        try {
            $checkProjectExistsOrNot = UtilityHelper::checkComponentSlugExistOrNot('project', $slug);
            if ($checkProjectExistsOrNot == false) {
                return $this->sendError(__('responses.project_not_found'), 404);
            }

            $addUpdateProjectSkillsRecruitingStatus = $this->projectRepository->addUpdateProjectSkillsRecruitingStatus($checkProjectExistsOrNot->id, $request);

            $participatesList = $this->projectMemberManagementRepository->addParticipates($checkProjectExistsOrNot, $request);
            if ((count($participatesList['invalid_emails']) > 0 || count($participatesList['already_members']) > 0) && count($participatesList['invited_emails']) < 1) {
                return $this->sendError($participatesList['add_participant_response'], 422);
            } elseif ($participatesList) {
                return $this->sendResponse($participatesList, $participatesList['add_participant_response']);
            }

            return $this->sendError(__('responses.create_member_manger_failed'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function acceptOrRejectJoinRequest(Request $request, $slug, $action)
    {
        try {
            $checkProjectExistsOrNot = UtilityHelper::checkComponentSlugExistOrNot('project', $slug);
            if ($checkProjectExistsOrNot == false) {
                return $this->sendError(__('responses.project_not_found'), 404);
            }

            $checkProjectStatus = $this->projectMemberManagementRepository->checkProjectJoinUnjoinStatus($request->email, $checkProjectExistsOrNot);
            if ($checkProjectStatus == false) {
                return $this->sendError(__('responses.project_sender_cannot_accept_request'), 404);
            }
            if ($checkProjectStatus) {
                $projectMemberManagement = $this->projectMemberManagementRepository->acceptOrRejectProjectJoinRequest($request, $checkProjectExistsOrNot, $action);
                if ($projectMemberManagement) {
                    return $this->sendResponse(null, __('responses.join_request_'.$action.'_successfully'));
                }
            }

            return $this->sendError(__('responses.request_not_exist'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function changeRole(Request $request)
    {
        try {
            if (!in_array($request->role, ['Team Leader', 'Editor', 'Viewer'])) {
                return $this->sendError(__('responses.role_not_exists'), 422);
            }

            $checkProjectExistsOrNot = UtilityHelper::checkComponentSlugExistOrNot('project', $request->slug);
            if ($checkProjectExistsOrNot == false) {
                return $this->sendError(__('responses.project_not_found'), 404);
            }

            $checkParticipantsUUID = $this->projectMemberManagementRepository->checkParticipantsUUID($checkProjectExistsOrNot->id, $request->uuid);
            if ($checkParticipantsUUID == false) {
                return $this->sendError(__('responses.project_member_invalid_uuid'), 404);
            }

            $checkCurrentProjectRole = $this->projectMemberManagementRepository->checkCurrentProjectRole($checkProjectExistsOrNot->id, $request->uuid, $request->role);
            if ($checkCurrentProjectRole == false) {
                return $this->sendError(__('responses.project_already_same_role'), 400);
            }

            $updateProjectRole = $this->projectMemberManagementRepository->updateProjectRole($checkProjectExistsOrNot->id, $request->uuid, $request->role);
            if ($updateProjectRole) {
                return $this->sendResponse(null, __('responses.project_role_update'), 200);
            }

            return $this->sendError(__('responses.request_not_exist'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function delete($slug, DeleteProjectMemberManagementRequest $request)
    {
        try {
            $checkProjectExistsOrNot = UtilityHelper::checkComponentSlugExistOrNot('project', $slug);
            if ($checkProjectExistsOrNot == false) {
                return $this->sendError(__('responses.project_not_found'), 404);
            }

            $participant_management = $this->projectMemberManagementRepository->deleteParticipates($checkProjectExistsOrNot, $request);
            if ($participant_management) {
                return $this->sendResponse(null, __('responses.participant_delete'));
            }

            return $this->sendError(__('responses.participant_not_delete'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function participantAcceptOrRejectJoinRequest($slug, $action)
    {
        try {
            $checkProjectExistsOrNot = UtilityHelper::checkComponentSlugExistOrNot('project', $slug);
            if ($checkProjectExistsOrNot == false) {
                return $this->sendError(__('responses.project_not_found'), 404);
            }

            $userEmail = auth()->user()->email;
            $checkProjectStatus = $this->projectMemberManagementRepository->checkParticipantProjectJoinUnjoinStatus($userEmail, $checkProjectExistsOrNot);
            if ($checkProjectStatus) {
                $projectMemberManagement = $this->projectMemberManagementRepository->participantAcceptOrRejectJoinRequest($userEmail, $checkProjectExistsOrNot, $action);
                if ($projectMemberManagement) {
                    return $this->sendResponse(null, __('responses.join_request_'.$action.'_successfully'));
                }
            }

            return $this->sendError(__('responses.request_not_exist'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
