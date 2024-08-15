<?php

namespace App\Traits\Maestro\Rank;

use App\Helpers\UtilityHelper;
use App\Services\Maestro\RankService;
use Exception;

trait RankTrait
{
    private function getRank()
    {
        try {
            $projectStatus = RankService::getRank();
            if ($projectStatus) {
                return $projectStatus;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function storeUpdateRank($request, $id, $moduleMode)
    {
        try {
            if (RankService::storeUpdateRank($request, $id, $moduleMode)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function findRank($id)
    {
        try {
            $projectStatus = RankService::findRank($id);
            if ($projectStatus) {
                return $projectStatus;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function deleteRank($projectStatus)
    {
        try {
            if (RankService::deleteRank($projectStatus)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
