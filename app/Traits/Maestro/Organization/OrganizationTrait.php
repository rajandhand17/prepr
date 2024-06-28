<?php

namespace App\Traits\Maestro\Organization;

use App\Services\Maestro\Organization\OrganizationService;
use Exception;

trait OrganizationTrait
{
    private function createOrganization($request)
    {
        try {
            if (Organizationservice::createOrganization($request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function updateOrganizationById($id, $request)
    {
        try {
            if (Organizationservice::updateOrganizationById($id, $request)) {
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
            $orgs = Organizationservice::getOrganizations();
            if ($orgs) {
                return $orgs;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
