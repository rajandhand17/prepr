<?php

namespace App\Repositories\Api\Dashboard\Organization;

use App\Helpers\UtilityHelper;
use App\Services\Manage\ChallengeAssessmentService;
use App\Services\Manage\OrganizationService;
use App\Services\ProjectService;
use App\Services\Public\ChallengeService;
use App\Services\Public\LabService;

class OrganizationDashboardRepository implements OrganizationDashboardInterface
{
    private $organizationService;
    private $labService;
    private $challengeService;
    private $projectService;
    private $challengeAssessmentService;

    public function __construct(OrganizationService $organizationService, LabService $labService, ChallengeService $challengeService, ProjectService $projectService, ChallengeAssessmentService $challengeAssessmentService)
    {
        $this->organizationService = $organizationService;
        $this->labService = $labService;
        $this->challengeService = $challengeService;
        $this->projectService = $projectService;
        $this->challengeAssessmentService = $challengeAssessmentService;
    }

    public function getOrganizationList($request)
    {
        try {
            return $this->organizationService->getOrganizationList($request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getLabList($request)
    {
        try {
            return $this->labService->getList($request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getChallengeList($request)
    {
        try {
            return $this->challengeService->getList($request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getMyProjectIds($userId)
    {
        try {
            return $this->projectService->getMyProjectIds($userId);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getAssessedProjectIds($userData)
    {
        try {
            $projectIds = [];
            $getAllChallengeIds = $this->challengeAssessmentService->getAllChallengeIds($userData);
            if (!empty($getAllChallengeIds)) {
                $projectIds = $this->projectService->getAssessedProjectIds($getAllChallengeIds, $userData);
            }

            return $projectIds;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getProjectList($getProjectIds, $request)
    {
        try {
            return $this->projectService->getProjectList($getProjectIds, $request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
