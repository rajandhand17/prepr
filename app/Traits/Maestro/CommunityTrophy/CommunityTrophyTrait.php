<?php

namespace App\Traits\Maestro\CommunityTrophy;

use App\Helpers\UtilityHelper;
use App\Services\Maestro\CommunityTrophyService;
use Exception;

trait CommunityTrophyTrait
{
    private function createCommunityTrophy($request)
    {
        try {
            if (CommunityTrophyService::createCommunityTrophy($request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function updateCommunityTrophyById($id, $request)
    {
        try {
            if (CommunityTrophyService::updateCommunityTrophyById($id, $request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function deleteCommunityTrophyById($id)
    {
        try {
            if (CommunityTrophyService::deleteCommunityTrophy($id)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getCommunityTrophy()
    {
        try {
            $CommunityTrophy = CommunityTrophyService::getCommunityTrophy();
            if ($CommunityTrophy) {
                return $CommunityTrophy;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
