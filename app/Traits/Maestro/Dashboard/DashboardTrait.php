<?php

namespace App\Traits\Maestro\Dashboard;

use App\Helpers\UtilityHelper;
use App\Services\Maestro\ChallengeService;
use App\Services\Maestro\LabService;
use App\Services\Maestro\LabProgramService;
use App\Services\Maestro\UserService;
use Exception;

trait DashboardTrait
{
    private function getComponentCount()
    {
        try {
            return ['totalChallenges' => ChallengeService::getChallengeCounts(), 'totalProjects' => LabProgramService::getProjectCounts(), 'totalUsers' => UserService::getUserCounts(), 'totalLabs' => LabService::getLabCounts()];
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
