<?php

namespace App\Traits\Maestro\Dashboard;

use App\Services\Maestro\Challenge\ChallengeService;
use App\Services\Maestro\LabService;
use App\Services\Maestro\ProjectService;
use App\Services\Maestro\UserService;
use Exception;

trait DashboardTrait
{
    private function getComponentCount()
    {
        try {
            return ['totalChallenges' => ChallengeService::getChallengeCounts(), 'totalProjects' => ProjectService::getProjectCounts(), 'totalUsers' => UserService::getUserCounts(), 'totalLabs' => LabService::getLabCounts()];
        } catch (Exception $e) {
            return false;
        }
    }
}
