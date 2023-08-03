<?php

namespace App\Repositories\Api\Public\Lab;

use App\Services\Public\LabService;
use App\Services\Public\LabSocialActivitiesService;

class LabRepository implements LabInterface
{
    private $LabService;
    private $labSocialActivitiesService;

    public function __construct(LabService $LabService, LabSocialActivitiesService $labSocialActivitiesService)
    {
        $this->LabService = $LabService;
        $this->labSocialActivitiesService = $labSocialActivitiesService;
    }
    public function getList($request)
    {
        try {
            $getLabList = $this->LabService->getLabList($request);
            return $getLabList;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabBasedOnSlug($slug)
    {
        try {
            return $this->LabService->getLabBasedOnSlug($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkSocialActivity($lab_id,$action)
    {
        try {
            switch ($action) {
                case 'join':
                    $column="join_unjoin";
                    $value="1";
                    break;
                case 'un-join':
                    $column="join_unjoin";
                    $value="2";
                    break;
               case 'follow':
                   $column="follow_unfollow";
                   $value="1";
                   break;
              case 'un-follow':
                   $column="follow_unfollow";
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
            }
            $response=$this->labSocialActivitiesService->checkSocialActivity($lab_id,$column,$value);
            if($response!==null){
                return $response;
            }
            return ["column"=>$column, "action"=>$value];
        } catch (\Exception $e) {
            return false;
        }
    }
    public function socialActivities($id,$column,$value): bool
    {
        try {
            return $this->labSocialActivitiesService->update($id,$column,$value);
        }catch (\Exception $e) {
            return false;
        }
    }
}
