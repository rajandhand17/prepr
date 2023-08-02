<?php

namespace App\Repositories\Api\Public\Organization;

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

    public function getOrganizationList($request)
    {
        try {
            return $this->organizationService->getOrganizationList($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getOrganizationBasedOnSlug($slug)
    {
        try {
            return $this->organizationService->getOrganizationBasedOnSlug($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkFollowUnfollowExists($id, $action)
    {
        try {
            $response = $this->organizationSocialActivitiesService->checkFollowUnfollowExists($id, $action);
            if ($response) {
                return $response;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkLikeUnlikeExists($id, $action)
    {
        try {
            $response = $this->organizationSocialActivitiesService->checkLikeUnlikeExists($id, $action);
            if ($response) {
                return $response;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function follow($organization_id)
    {
        try {
            return $this->organizationSocialActivitiesService->follow($organization_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function unfollow($organization_id)
    {
        try {
            return $this->organizationSocialActivitiesService->unfollow($organization_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function like($organization_id)
    {
        try {
            return $this->organizationSocialActivitiesService->like($organization_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function unlike($organization_id)
    {
        try {
            return $this->organizationSocialActivitiesService->unlike($organization_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function share($organization_id)
    {
        try {
            return $this->organizationSocialActivitiesService->share($organization_id);
        } catch (\Exception $e) {
            return $e;

            return false;
        }
    }
}
