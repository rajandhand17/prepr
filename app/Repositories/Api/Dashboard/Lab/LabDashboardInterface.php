<?php

namespace App\Repositories\Api\Dashboard\Lab;

interface LabDashboardInterface
{
    public function fetchChallengeReportBasedOnOrganization($organizationId, $userData);

    public function fetchLabReportBasedOnOrganization($organizationId, $userData);

    public function fetchResourceReportBasedOnOrganization($organizationId, $userData);

    public function fetchProjectReportBasedOnOrganization($organizationId, $userData);

    public function checkOrganizationPlan($organizationData);

    public function fetchChallengesBasedOnOrganizationId($organizationId);

    public function fetchManagersUpComingDeadlineChallenges($challengeData);

    public function fetchAssessmentProjectids($challengeIds, $userData);

    public function fetchSubmittedProjectids($challengeIds);

    public function fetchProjectList($projectIds);

    public function dashboardInboxList($userData);

    public function dashboardFriendList($userData);

    public function fetchUserSkills($userData);

    public function fetchRecommendedChallenges($fetchUserSkills, $userData);

    public function fetchRecommendedLabs($fetchUserSkills, $userData);

    public function fetchRecommendedResourceModules($fetchUserSkills, $userData);

    public function getChallengeDashboardList($request, $organization);

    public function getLabDashboardList($request, $organization);

    public function getResourceModuleDashboardList($request, $organization);

    public function fetchDashboardLayout($userData, $dashboardType);

    public function storeStaticDefaultLayout($userData, $dashboardType);

    public function updateDashboardLayout($request, $userData, $dashboardType);
}
