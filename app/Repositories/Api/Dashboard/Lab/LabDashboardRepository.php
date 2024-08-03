<?php

namespace App\Repositories\Api\Dashboard\Lab;

use App\Helpers\UtilityHelper;
use App\Services\ChallengeAssessmentUserService;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabProgramService;
use App\Services\Manage\LabService;
use App\Services\Manage\MemberManagementService;
use App\Services\Manage\ResourceCollectionService;
use App\Services\Manage\ResourceGroupService;
use App\Services\Manage\ResourceModuleService;
use App\Services\ModuleCompletionStatusService;
use App\Services\ProjectService;
use Exception;

class LabDashboardRepository implements LabDashboardInterface
{
    private $memberManagementService;
    private $challengeService;
    private $labService;
    private $labProgramService;
    private $resourceModuleService;
    private $resourceCollectionService;
    private $resourceGroupService;
    private $moduleCompletionStatusService;
    private $projectService;
    private $challengeAssessmentUserService;

    public function __construct(MemberManagementService $memberManagementService, ChallengeService $challengeService, LabService $labService, LabProgramService $labProgramService, ResourceModuleService $resourceModuleService, ResourceCollectionService $resourceCollectionService, ResourceGroupService $resourceGroupService, ModuleCompletionStatusService $moduleCompletionStatusService, ProjectService $projectService, ChallengeAssessmentUserService $challengeAssessmentUserService)
    {
        $this->memberManagementService = $memberManagementService;
        $this->challengeService = $challengeService;
        $this->labService = $labService;
        $this->labProgramService = $labProgramService;
        $this->resourceModuleService = $resourceModuleService;
        $this->resourceCollectionService = $resourceCollectionService;
        $this->resourceGroupService = $resourceGroupService;
        $this->moduleCompletionStatusService = $moduleCompletionStatusService;
        $this->projectService = $projectService;
        $this->challengeAssessmentUserService = $challengeAssessmentUserService;
    }

    public function fetchChallengeReportBasedOnOrganization($organizationId)
    {
        try {
            $fetchChallenges = $this->challengeService->fetchChallengeReportBasedOnOrganization($organizationId);
            $totalChallengesCount = $fetchChallenges->count();
            $totalActiveChallengesCount = $fetchChallenges->where('is_open', '0')->count();
            $totalCloseChallengesCount = $fetchChallenges->where('is_open', '1')->count();
            $totalCompletedChallengesCount = $fetchChallenges->where('is_open', '2')->count();
            $moduleType = config('constants.module_component_type.challenge');
            $totalActiveMembersCountBasedOnChallengeIds = $this->memberManagementService->totalActiveMembersCountBasedOnModuleIds($fetchChallenges->pluck('id'), $moduleType)->count();

            $fetchChallengeReportBasedOnOrganization = ['totalChallenges' => $totalChallengesCount, 'totalActiveChallenges' => $totalActiveChallengesCount, 'totalCloseChallenges' => $totalCloseChallengesCount, 'totalCompletedChallenges' => $totalCompletedChallengesCount, 'totalActiveMembers' => $totalActiveMembersCountBasedOnChallengeIds];
            return $fetchChallengeReportBasedOnOrganization;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchLabReportBasedOnOrganization($organizationId)
    {
        try {
            $fetchLabs = $this->labService->fetchLabReportBasedOnOrganization($organizationId);
            $totalLabsCount = $fetchLabs->count();
            $moduleType = config('constants.module_component_type.lab');
            $totalActiveMembersCountBasedOnLabIds = $this->memberManagementService->totalActiveMembersCountBasedOnModuleIds($fetchLabs->pluck('id'), $moduleType)->count();
            $totalLabProgramsCount = $this->labProgramService->fetchLabProgramReportBasedOnOrganization($organizationId);

            $fetchLabReportBasedOnOrganization = ['totalLabs' => $totalLabsCount, 'totalLabPrograms' => $totalLabProgramsCount->count(), 'totalActiveMembers' => $totalActiveMembersCountBasedOnLabIds];
            return $fetchLabReportBasedOnOrganization;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchResourceReportBasedOnOrganization($organizationId)
    {
        try {
            $fetchResourceModuleBasedOnOrganizationId = $this->resourceModuleService->fetchResourceModuleReportBasedOnOrganization($organizationId);
            $totalViewersCountBasedOnResourceModuleIds = $this->moduleCompletionStatusService->totalViewersCountBasedOnResourceModuleIds($fetchResourceModuleBasedOnOrganizationId->pluck('id'));

            $fetchResourceCollectionBasedOnOrganizationId = $this->resourceCollectionService->fetchResourceCollectionReportBasedOnOrganization($organizationId);
            $fetchResourceGroupBasedOnOrganizationId = $this->resourceGroupService->fetchResourceGroupReportBasedOnOrganization($organizationId);

            $fetchResourceReportBasedOnOrganization = ['totalResourceModule' => $fetchResourceModuleBasedOnOrganizationId->count(), 'totalResourceCollection' => $fetchResourceCollectionBasedOnOrganizationId->count(), 'totalResourceGroup' => $fetchResourceGroupBasedOnOrganizationId->count(), 'totalViewers' => $totalViewersCountBasedOnResourceModuleIds];
            return $fetchResourceReportBasedOnOrganization;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchProjectReportBasedOnOrganization($organizationId)
    {
        try {
            $fetchChallenges = $this->challengeService->fetchChallengeReportBasedOnOrganization($organizationId);
            $fetchProjectBasedOnChallengeIds = $this->projectService->fetchProjectBasedOnChallengeIds($fetchChallenges->pluck('id'));
            $totalInProgressProjects = $fetchProjectBasedOnChallengeIds->where('is_submitted', '0')->count();
            $totalSubmittedProjects = $fetchProjectBasedOnChallengeIds->where('is_submitted', '1')->count();
            $totalAssessedProjectsBasedOnProjectIds = $this->challengeAssessmentUserService->totalAssessedProjectsBasedOnProjectIds($fetchProjectBasedOnChallengeIds->pluck('id'))->unique()->count();
            $totalNonAssessedProjectsBasedOnProjectIds = $totalSubmittedProjects - $totalAssessedProjectsBasedOnProjectIds;

            $fetchProjectReportBasedOnOrganization = ['totalInProgressProjects' => $totalInProgressProjects, 'totalSubmittedProjects' => $totalSubmittedProjects, 'totalAssessedProjects' => $totalAssessedProjectsBasedOnProjectIds, 'totalNonAssessedProjects' => $totalNonAssessedProjectsBasedOnProjectIds];
            return $fetchProjectReportBasedOnOrganization;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
