<?php

namespace App\Repositories\Api\Dashboard\Lab;

interface LabDashboardInterface
{
    public function fetchChallengeReportBasedOnOrganization($organizationId);

    public function fetchLabReportBasedOnOrganization($organizationId);

    public function fetchResourceReportBasedOnOrganization($organizationId);

    public function fetchProjectReportBasedOnOrganization($organizationId);

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

    public function getChallengeList($request, $organization);

    public function getLabList($request, $organization);

    public function getResourceModuleList($request, $organization);
}
