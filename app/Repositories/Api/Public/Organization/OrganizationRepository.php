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

    public function getList($request){
        try{
            return $this->organizationService->getList($request);
        }catch(\Exception $e){
        return false;
        }
    }

    public function getOrganizationBasedOnSlug($slug){
        try{
            return $this->organizationService->getOrganizationBasedOnSlug($slug);
        }catch(\Exception $e){
            return false;
        }
    }
    public function socialActivity($id,$column,$action): bool
    {
        try{
           return $this->organizationSocialActivitiesService->update($id,$column,$action);
        }catch(\Exception $e){
            return false;
        }
    }
    public function checkSocialActivity($lab_id,$action)
    {
        try {
            switch ($action) {
                case 'follow':
                    $column = 'follow_unfollow';
                    $value = '1';
                    break;
                case 'un-follow':
                    $column = 'follow_unfollow';
                    $value = '2';
                    break;
                case 'share':
                    $column = 'share';
                    $value = '1';
                    break;
                case 'favourite':
                    $column="favourite";
                    $value="1";
                    break;
                case 'un-favourite':
                    $column="favourite";
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
                default:
                    return false;
                    break;
            }
            $response=$this->organizationSocialActivitiesService->checkSocialActivity($lab_id,$column,$value);
            if($response!==null){
                return $response;
            }
            return ['column'=>$column, 'action'=>$value];
        } catch (\Exception $e) {
            return false;
        }
    }
}
