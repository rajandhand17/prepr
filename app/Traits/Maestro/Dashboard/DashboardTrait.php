<?php

namespace App\Traits\Maestro\Dashboard;

use App\Services\Maestro\Dashboard\DashboardService;
use Exception;

trait DashboardTrait
{
    private function getComponentCount()
    {
        try {
            $componentCount = DashboardService::getComponentCount();
            if ($componentCount) {
                return $componentCount;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
