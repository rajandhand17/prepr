<?php

namespace App\Repositories\Api\Dashboard\Organization;

use App\Helpers\UtilityHelper;
use App\Services\ChallengeAssessmentUserService;
use App\Services\Chat\ConversationService;
use App\Services\DashboardLayoutService;
use App\Services\FriendService;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabProgramService;
use App\Services\Manage\LabService;
use App\Services\Manage\MemberManagementService;
use App\Services\Manage\OrganizationService;
use App\Services\Manage\ResourceCollectionService;
use App\Services\Manage\ResourceGroupService;
use App\Services\Manage\ResourceModuleService;
use App\Services\ModuleCompletionStatusService;
use App\Services\ProjectService;
use App\Services\Public\ChallengeService as PublicChallengeService;
use App\Services\Public\LabService as PublicLabService;
use App\Services\Public\ResourceModuleService as PublicResourceModuleService;
use App\Services\UserSkillsService;
use Exception;

class OrganizationDashboardRepository implements OrganizationDashboardInterface
{
    private $challengeService;
    private $memberManagementService;
    private $labService;
    private $labProgramService;
    private $resourceModuleService;
    private $resourceCollectionService;
    private $resourceGroupService;
    private $moduleCompletionStatusService;
    private $projectService;
    private $challengeAssessmentUserService;
    private $organizationService;
    private $conversationService;
    private $friendService;
    private $userSkillsService;
    private $publicChallengeService;
    private $publicLabService;
    private $publicResourceModuleService;
    private $dashboardLayoutService;

    public function __construct(ChallengeService $challengeService, MemberManagementService $memberManagementService, LabService $labService, LabProgramService $labProgramService, ResourceModuleService $resourceModuleService, ResourceCollectionService $resourceCollectionService, ResourceGroupService $resourceGroupService, ModuleCompletionStatusService $moduleCompletionStatusService, ProjectService $projectService, ChallengeAssessmentUserService $challengeAssessmentUserService, OrganizationService $organizationService, ConversationService $conversationService, FriendService $friendService, UserSkillsService $userSkillsService, PublicChallengeService $publicChallengeService, PublicLabService $publicLabService, PublicResourceModuleService $publicResourceModuleService, DashboardLayoutService $dashboardLayoutService)
    {
        $this->challengeService = $challengeService;
        $this->memberManagementService = $memberManagementService;
        $this->labService = $labService;
        $this->labProgramService = $labProgramService;
        $this->resourceModuleService = $resourceModuleService;
        $this->resourceCollectionService = $resourceCollectionService;
        $this->resourceGroupService = $resourceGroupService;
        $this->moduleCompletionStatusService = $moduleCompletionStatusService;
        $this->projectService = $projectService;
        $this->challengeAssessmentUserService = $challengeAssessmentUserService;
        $this->organizationService = $organizationService;
        $this->conversationService = $conversationService;
        $this->friendService = $friendService;
        $this->userSkillsService = $userSkillsService;
        $this->publicChallengeService = $publicChallengeService;
        $this->publicLabService = $publicLabService;
        $this->publicResourceModuleService = $publicResourceModuleService;
        $this->dashboardLayoutService = $dashboardLayoutService;
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

    public function checkOrganizationPlan($organizationData)
    {
        try {
            return $this->organizationService->planData($organizationData);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchChallengesBasedOnOrganizationId($organizationId)
    {
        try {
            return $this->challengeService->fetchChallengeReportBasedOnOrganization($organizationId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchManagersUpComingDeadlineChallenges($challengeData)
    {
        try {
            return $this->challengeService->fetchManagersUpComingDeadlineChallenges($challengeData);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchAssessmentProjectids($challengeIds, $userData)
    {
        try {
            return $this->projectService->getPendingProjectIds($challengeIds, $userData);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchSubmittedProjectids($challengeIds)
    {
        try {
            return $this->projectService->fetchSubmittedProjectids($challengeIds);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchProjectList($projectIds)
    {
        try {
            return $this->projectService->getDashboardProjectList($projectIds);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function dashboardInboxList($userData)
    {
        try {
            return $this->conversationService->dashboardInboxList($userData);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function dashboardFriendList($userData)
    {
        try {
            return $this->friendService->dashboardFriendList($userData);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchUserSkills($userData)
    {
        try {
            return $this->userSkillsService->fetchUserSkills($userData);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchRecommendedChallenges($fetchUserSkills, $userData)
    {
        try {
            return $this->publicChallengeService->fetchRecommendedChallenges($fetchUserSkills, $userData);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchRecommendedLabs($fetchUserSkills, $userData)
    {
        try {
            return $this->publicLabService->fetchRecommendedLabs($fetchUserSkills, $userData);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchRecommendedResourceModules($fetchUserSkills, $userData)
    {
        try {
            return $this->publicResourceModuleService->fetchRecommendedResourceModules($fetchUserSkills, $userData);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getChallengeList($request, $organization)
    {
        try {
            return $this->challengeService->getChallengeList($request, $organization);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getLabList($request, $organization)
    {
        try {
            return $this->labService->getLabList($request, $organization);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getResourceModuleList($request, $organization)
    {
        try {
            return  $this->resourceModuleService->getResourceModuleList($request, $organization);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchOrganizationIds($userData)
    {
        try {
            $userId = $userData->id;
            $userEmail = $userData->email;
            $role = 'Organization Manager';
            $inviteStatus = config('constants.member_management_invite_status.accepted');
            $fetchOrganizationIds = collect();

            $fetchOwnOrganizationIds = $this->organizationService->fetchOwnOrganizationIds($userId);
            $fetchMemberOrganizationIds = $this->memberManagementService->fetchMemberOrganizationIds($userEmail, $role, $inviteStatus);

            $fetchOrganizationIds = $fetchOwnOrganizationIds->merge($fetchMemberOrganizationIds);

            return $fetchOrganizationIds->unique()->values();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchOrganizations($request, $fetchOrganizationIds)
    {
        try {
            return $this->organizationService->fetchOrganizations($request, $fetchOrganizationIds);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchDashboardLayout($userData, $dashboardType)
    {
        try {
            return $this->dashboardLayoutService->fetchDashboardLayout($userData, $dashboardType);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateDashboardLayout($request, $userData, $dashboardType)
    {
        try {
            return $this->dashboardLayoutService->updateDashboardLayout($request, $userData, $dashboardType);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
