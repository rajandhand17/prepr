<?php

namespace App\Repositories\Api\Leaderboard;

interface LeaderboardInterface
{
    public function getLeaderBoardList($request);

    public function getComponentsMembers($slug,$component,$request);
}
