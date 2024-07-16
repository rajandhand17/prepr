<?php

namespace App\Traits\Maestro\Dashboard;

use App\Services\Maestro\User\UserService;
use App\Services\Maestro\Project\ProjectService;
use App\Services\Maestro\Challenge\ChallengeService;
use App\Services\Maestro\LabService;
use Exception;

trait DashboardTrait
{
    private function getComponentCount()
    {
        try {
            return ['totalChallenges' => ChallengeService::getChallengeCounts(), 'totalProjects' => ProjectService::getProjectCounts(), 'totalUsers' => UserService::getUserCounts() , 'totalLabs' => LabService::getLabCounts()];
        } catch (Exception $e) {
            return false;
        }
    }
}
