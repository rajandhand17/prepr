<?php

namespace App\Traits\Maestro\TrophyAwards;

use App\Services\Maestro\TrophyAwards\TrophyAwardsService;
use Exception;

trait TrophyAwardsTrait
{
    private function createTrophyAwards($request)
    {
        try {
            if (TrophyAwardsService::createTrophyAwards($request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function updateTrophyAwardsById($id, $request)
    {
        try {
            if (TrophyAwardsService::updateTrophyAwardsById($id, $request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function deleteTrophyAwardsById($id)
    {
        try {
            if (TrophyAwardsService::deleteTrophyAwards($id)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getTrophyAwards()
    {
        try {
            $trophyawards = TrophyAwardsService::getTrophyAwards();
            if ($trophyawards) {
                return $trophyawards;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
