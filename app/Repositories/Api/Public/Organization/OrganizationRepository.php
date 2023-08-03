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
    public function socialActivities($id,$column,$action): bool
    {
        try {
            $response = $this->organizationSocialActivitiesService->update($id,$column,$action);
            if ($response) {
                return $response;
            }
            return false;
        }catch (\Exception $e){
        return false;
        }
    }
    public function checkExists($id, $action)
    {
        try {
            $column="";
            $value="";
            switch ($action) {
                case 'follow':
                    $column="follow_unfollow";
                    $value="1";
                    break;
                case 'un-follow':
                    $column="follow_unfollow";
                    $value="2";
                    break;
                case 'like':
                    $column="like_dislike";
                    $value="1";
                    break;
                case 'un-like':
                    $column="like_dislike";
                    $value="2";
                    break;
                case 'share':
                    $column="share";
                    $value="1";
                    break;
                case 'favourite':
                    $column="favourite";
                    $value="1";
                    break;
                case 'un-favourite':
                    $column="favourite";
                    $value="2";
                    break;
                default:
                    return false;
                    break;
            };
            $response = $this->organizationSocialActivitiesService->checkExists($id,$column, $value);
            if ($response) {
                return $response;
            }
            $response=["column"=>$column, "action"=>$value];
            return $response;
        } catch (\Exception $e) {
            return false;
        }
    }
}
