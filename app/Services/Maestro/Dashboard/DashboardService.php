<?php

namespace App\Services\Maestro\Dashboard;

use App\Models\Challenge;
use App\Models\Lab;
use App\Models\Project;
use App\Models\User;
use Exception;

class DashboardService
{
    public static function getComponentCount()
    {
        try {
            $totalUser = User::count();
            $totalLabs = Lab::count();
            $totalProjects = Project::count();
            $totalChallenge = Challenge::count();

            return ['totalUser' => $totalUser ?? 0, 'totalLabs' => $totalLabs ?? 0, 'totalProjects' => $totalProjects ?? 0, 'totalChallenge' => $totalChallenge ?? 0];
        } catch (Exception $e) {
            return false;
        }
    }
}
