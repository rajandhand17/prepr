<?php

namespace App\Http\Controllers\Api\Dashboard\Organization;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Manage\Lab\LabResource;
use App\Http\Resources\Manage\Organization\OrganizationResource;
use App\Http\Resources\Project\ProjectResource;
use App\Http\Resources\Public\Challenge\ChallengeResource;
use App\Repositories\Api\Dashboard\Organization\OrganizationDashboardRepository;
use App\Services\Manage\OrganizationService;
use Illuminate\Http\Request;

class OrganizationDashboardController extends AppBaseController
{
    private $organizationDashboardRepository;

    public function __construct(OrganizationDashboardRepository $organizationDashboardRepository)
    {
        $this->organizationDashboardRepository = $organizationDashboardRepository;
    }

    public function getMyOrganizations(Request $request)
    {
        try {
            $organization = $this->organizationDashboardRepository->getOrganizationList($request);
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

    public function getMyLabs(Request $request)
    {
        try {
            if ($request->organization_id && is_array($request->organization_id)) {
                $organization = OrganizationService::getOrganizationExistBasedOnUuidArray($request->organization_id)->pluck('id');
                if (!$organization->count() > 0) {
                    return $this->sendError(__('responses.organization_not_found'), 404);
                }
            }
            $lab = $this->organizationDashboardRepository->getLabList($request);
            if ($lab !== false) {
                $response = [
                    'total_count'  => $lab->total(),
                    'per_page'     => $lab->perPage(),
                    'count'        => $lab->count(),
                    'current_page' => $lab->currentPage(),
                    'total_pages'  => $lab->lastPage(),
                    'list'         => LabResource::collection($lab),
                ];

                return $this->sendResponse($response, __('responses.found_labs_list'));
            }

            return $this->sendError(__('responses.not_found_labs_list'), 404);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getMyChallenges(Request $request)
    {
        try {
            if ($request->organization_id && is_array($request->organization_id)) {
                $organization = OrganizationService::getOrganizationExistBasedOnUuidArray($request->organization_id)->pluck('id');
                if (!$organization) {
                    return $this->sendError(__('responses.organization_not_found'), 404);
                }
            }
            $challenges = $this->organizationDashboardRepository->getChallengeList($request);
            if ($challenges !== false) {
                $response = [
                    'total_count'  => $challenges->total(),
                    'per_page'     => $challenges->perPage(),
                    'count'        => $challenges->count(),
                    'current_page' => $challenges->currentPage(),
                    'total_pages'  => $challenges->lastPage(),
                    'list'         => ChallengeResource::collection($challenges),
                ];

                return $this->sendResponse($response, __('responses.found_challenges_list'));
            }

            return $this->sendError(__('responses.not_found_challenges_list'), 404);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getMyProjects(Request $request)
    {
        try {
            if (!in_array($request->type, ['my', 'assessed'])) {
                return $this->sendError(__('responses.handler_bad_request'), 402);
            }

            switch ($request->type) {
                case 'my':
                    $getProjectIds = $this->organizationDashboardRepository->getMyProjectIds(auth()->user()->id);
                    break;

                case 'assessed':
                    $getProjectIds = $this->organizationDashboardRepository->getAssessedProjectIds(auth()->user());
                    break;
                default:
                    return $this->sendError(__('responses.handler_bad_request'), 402);
                    break;
            }
            if ($getProjectIds) {
                $project = $this->organizationDashboardRepository->getProjectList($getProjectIds, $request);
                if ($project !== false) {
                    $response = [
                        'total_count'  => $project->total(),
                        'per_page'     => $project->perPage(),
                        'count'        => $project->count(),
                        'current_page' => $project->currentPage(),
                        'total_pages'  => $project->lastPage(),
                        'list'         => ProjectResource::collection($project),
                    ];

                    return $this->sendResponse($response, __('responses.found_projects_list'));
                }
            }

            return $this->sendError(__('responses.not_found_projects_list'), 404);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
