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

    public function projectRequestIds($userData, $inviteStatus);

    public function projectFavouriteIds($userData);

    public function getDashboardProjectList($projectIds);
}
