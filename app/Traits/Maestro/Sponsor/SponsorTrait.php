<?php

namespace App\Traits\Maestro\Sponsor;

use App\Helpers\UtilityHelper;
use App\Services\Maestro\HostService;
use Exception;

trait SponsorTrait
{
    private function getSponsorList()
    {
        try {
            $sponsorList = HostService::getSponsorList();
            if ($sponsorList) {
                return $sponsorList;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function createSponsor($request)
    {
        try {
            if (HostService::createSponsor($request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function deleteSponsorById($id)
    {
        try {
            if (HostService::deleteSponsor($id)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function getSponsorById($id)
    {
        try {
            return HostService::getSponsorById($id);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function updateSponsorById($id, $request)
    {
        try {
            if (HostService::updateSponsorById($id, $request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
