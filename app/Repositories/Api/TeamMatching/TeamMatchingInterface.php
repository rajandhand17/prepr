<?php

namespace App\Repositories\Api\TeamMatching;

interface TeamMatchingInterface
{
    public function getBrowsersList($request);

    public function getPendingRequests($userData);

    public function getMatchingTeams();
}
