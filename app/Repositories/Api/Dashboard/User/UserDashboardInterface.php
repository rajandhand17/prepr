<?php

namespace App\Repositories\Api\Dashboard\User;

interface UserDashboardInterface
{
    public function challengeRequestIds($userData, $inviteStatus);

    public function challengeFavouriteIds($userData);

    public function getChallengeList($challengeIds);

    public function labRequestIds($userData, $inviteStatus);

    public function labFavouriteIds($userData);

    public function getLabList($labIds);

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
}
