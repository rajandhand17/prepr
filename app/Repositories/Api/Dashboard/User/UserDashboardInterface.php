<?php

namespace App\Repositories\Api\Dashboard\User;

interface UserDashboardInterface
{
    public function challengeRequestIds($userData, $inviteStatus);

    public function challengeFavouriteIds($userData);

    public function getChallengeList($challengeIds);
}
