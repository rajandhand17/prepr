<?php

namespace App\Http\Controllers\Api\Manage\ProjectMemberManagement;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\ProjectMemberManagement\CreateProjectMemberManagementRequest;
use App\Http\Resources\EmailTemplate\EmailTemplateResource;
use App\Http\Resources\Manage\ProjectMemberManagement\ProjectMemberManagementResource;
use App\Repositories\Api\Manage\ProjectMemberManagement\ProjectMemberManagementRepository;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;

class ProjectMemberManagementController extends AppBaseController
{
    private $projectMemberManagementRepository;

    public function __construct(ProjectMemberManagementRepository $projectMemberManagementRepository)
    {
        $this->projectMemberManagementRepository = $projectMemberManagementRepository;
    }

    public function index($slug, Request $request)
    {
        try {
            $checkProjectExistsOrNot = UtilityHelper::checkComponentSlugExistOrNot('project', $slug);
            if ($checkProjectExistsOrNot == false) {
                return $this->sendResponse([], __('responses.project_not_found'), 403);
            }

            $projectMemberManagementListing = $this->projectMemberManagementRepository->getProjectBasedParticipants($checkProjectExistsOrNot, $request);
            $getTemplate = $this->projectMemberManagementRepository->getTemplate($request->language);

            if ($getTemplate) {
                $user_name = UserService::joinName(auth()->user()->first_name, auth()->user()->last_name);
                $getTemplate->body_content = str_replace('user_name', $user_name, str_replace('component_title', $checkProjectExistsOrNot->title, $getTemplate->body_content));
            }

            $response = [
                'id'                          => $checkProjectExistsOrNot->uuid,
                'title'                       => $checkProjectExistsOrNot->title,
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

                return $this->sendResponse($response, __('responses.participant_list_found'));
            }

            return $this->sendResponse($response, __('responses.participant_list_not_found'));
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
    public function downloadSample()
    {
        try {
            return $this->projectMemberManagementRepository->downloadSample();
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create($slug, CreateProjectMemberManagementRequest $request)
    {
        try {
            $checkProjectExistsOrNot = UtilityHelper::checkComponentSlugExistOrNot('project', $slug);
            if ($checkProjectExistsOrNot == false) {
                return $this->sendResponse([], __('responses.project_not_found'), 403);
            }
            $participatesList = $this->projectMemberManagementRepository->addParticipates($checkProjectExistsOrNot, $request);
            if ((count($participatesList['invalid_emails']) > 0 || count($participatesList['already_members']) > 0) && count($participatesList['invited_emails']) < 1) {
                return $this->sendError($participatesList['add_participant_response'], 403);
            } elseif ($participatesList) {
                return $this->sendResponse($participatesList, $participatesList['add_participant_response']);
            }

            return $this->sendError(__('responses.create_member_manger_failed'), 403);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
