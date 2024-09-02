<?php

namespace App\Repositories\Api\Dashboard\User;

interface UserDashboardInterface
{
    public function challengeRequestIds($userData, $inviteStatus);

    public function challengeFavouriteIds($userData);

    public function getChallengeDashboardList($challengeIds);

    public function labRequestIds($userData, $inviteStatus);

    public function labFavouriteIds($userData);

    public function getLabDashboardList($labIds);

    public function myProjectDashboardRequestIds($userData, $inviteStatus);

    public function invitesProjectDashboardRequestIds($userData, $inviteStatus);

    public function projectFavouriteIds($userData);

    public function getDashboardProjectList($projectIds);

    public function myResourceModuleIds($userData);

    public function resourceModuleFavouriteIds($userData);

    public function getResourceModuleDashboardList($resourceModuleIds);

    public function getMyLatestAchievement($userData);

    public function fetchUserSkills($userData);

    public function fetchRecommendedChallenges($fetchUserSkills, $userData);

    public function fetchRecommendedLabs($fetchUserSkills, $userData);

    public function fetchRecommendedResourceModules($fetchUserSkills, $userData);

    public function fetchMyChallengeProgress($userData);

    public function fetchMyLabProgress($userData);

    public function fetchMyResourceModuleProgress($userData);

    public function fetchUpComingDeadlineChallenges($challengeIds, $userData);

    public function dashboardInboxList($userData);

    public function userDashboardFriendList($userData);

    public function fetchLastVisited($userData);

    public function fetchUserDashboardLayout($userData, $dashboardType);

    public function updateUserDashboardLayout($request, $userData, $dashboardType);
}
