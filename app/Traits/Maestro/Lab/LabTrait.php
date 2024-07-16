<?php

namespace App\Traits\Maestro\Lab;

use App\Services\Maestro\LabService;

trait LabTrait
{
    protected $labService;

    public function getLabsBasedOnOrganizations($request)
    {
        try {
            $labList = LabService::getLabBasedOnOrganization($request);
            if ($labList) {
                return $labList;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
