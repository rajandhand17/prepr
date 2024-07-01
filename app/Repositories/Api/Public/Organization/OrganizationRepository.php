<?php

namespace App\Repositories\Api\Public\Organization;

use App\Helpers\UtilityHelper;
use App\Services\Public\OrganizationService;
use App\Services\Public\OrganizationSocialActivitiesService;

class OrganizationRepository implements OrganizationInterface
{
    private $organizationService;
    private $organizationSocialActivitiesService;

    public function __construct(OrganizationService $organizationService, OrganizationSocialActivitiesService $organizationSocialActivitiesService)
    {
        $this->organizationService = $organizationService;
        $this->organizationSocialActivitiesService = $organizationSocialActivitiesService;
    }

    public function getList($request)
    {
        try {
            return $this->organizationService->getList($request);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getOrganizationBasedOnSlug($slug)
    {
        try {
            return $this->organizationService->getOrganizationBasedOnSlug($slug);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getColumnNameValue($action)
    {
        try {
            return $this->organizationSocialActivitiesService->getColumnNameValue($action);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkSocialActivity($organization_id, $column, $action)
    {
        try {
            return $this->organizationSocialActivitiesService->checkSocialActivity($organization_id, $column, $action);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function captureSocialActivity($organization_id, $column, $action)
    {
        try {
            return $this->organizationSocialActivitiesService->captureSocialActivity($organization_id, $column, $action);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
