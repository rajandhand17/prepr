<?php

namespace App\Traits\Maestro\Organization;

use App\Services\Maestro\Organization\OrganizationService;
use Exception;

trait OrganizationTrait
{
    private function createOrganization($request)
    {
        try {
            if (OrganizationService::createOrganization($request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            dd($e);

            return false;
        }
    }

    private function updateOrganizationById($id, $request)
    {
        try {
            if (OrganizationService::updateOrganizationById($id, $request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function deleteOrganizationById($id)
    {
        try {
            if (OrganizationService::deleteOrganization($id)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getOrganizations()
    {
        try {
            $orgs = OrganizationService::getOrganizations();
            if ($orgs) {
                return $orgs;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
