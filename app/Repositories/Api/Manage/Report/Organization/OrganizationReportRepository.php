<?php

namespace App\Repositories\Api\Manage\Report\Organization;

use App\Helpers\UtilityHelper;
use App\Models\Organization;
use App\Services\Manage\Report\OrganizationReportService;

class OrganizationReportRepository implements OrganizationReportInterface
{
    public function __construct(protected OrganizationReportService $organizationReportService)
    {
    }

    public function getPaginatedChallenges(Organization $organization): false|array
    {
        try {
            return $this->organizationReportService->getPaginatedChallenges($organization);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getOrganizationEngagements(Organization $organization): false|array
    {
        try {
            return $this->organizationReportService->getOrganizationEngagements($organization);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getOrganizationMembers(Organization $organization): false|array
    {
        try {
            return $this->organizationReportService->getOrganizationMembers($organization);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedChallengePath(Organization $organization): false|array
    {
        try {
            return $this->organizationReportService->getPaginatedChallengePath($organization);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedResourceModule(Organization $organization): false|array
    {
        try {
            return $this->organizationReportService->getPaginatedResourceModule($organization);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedLabs(Organization $organization): false|array
    {
        try {
            return $this->organizationReportService->getPaginatedLabs($organization);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedLabPrograms(Organization $organization): false|array
    {
        try {
            return $this->organizationReportService->getPaginatedLabPrograms($organization);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedResourceCollection(Organization $organization): false|array
    {
        try {
            return $this->organizationReportService->getPaginatedResourceCollection($organization);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedResourceGroup(Organization $organization): false|array
    {
        try {
            return $this->organizationReportService->getPaginatedResourceGroup($organization);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getOrganizationMemberActivity(Organization $organization): false|array
    {
        try {
            return $this->organizationReportService->organizationMemberActivity($organization);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
