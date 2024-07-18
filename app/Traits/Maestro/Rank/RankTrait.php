<?php

namespace App\Traits\Maestro\Rank;

use App\Services\Maestro\Rank\RankService;
use Exception;

trait RankTrait
{
    private function getLanguage()
    {
        try {
            $languages = RankService::getLanguage();
            if ($languages) {
                return $languages;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getRank()
    {
        try {
            $projectStatus = RankService::getRank();
            if ($projectStatus) {
                return $projectStatus;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getRankStatus()
    {
        try {
            $status = RankService::getRankStatus();
            if ($status) {
                return $status;
            }

            return false;
        } catch (Exception $e) {
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
            return false;
        }
    }
}
