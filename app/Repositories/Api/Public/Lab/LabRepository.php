<?php

namespace App\Repositories\Api\Public\Lab;

use App\Services\Public\LabService;
use App\Services\Public\LabSocialActivitiesService;
use App\Services\Manage\MemberManagementService;
class LabRepository implements LabInterface
{
    private $LabService;
    private $labSocialActivitiesService;
    private $memberManagementService;

    public function __construct(LabService $LabService, LabSocialActivitiesService $labSocialActivitiesService,MemberManagementService $memberManagementService)
    {
        $this->LabService = $LabService;
        $this->labSocialActivitiesService = $labSocialActivitiesService;
        $this->memberManagementService = $memberManagementService;
    }

    public function getList($request)
    {
        try {
            return $this->LabService->getList($request);
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

    public function getColumnNameValue($action)
    {
        try {
            return $this->labSocialActivitiesService->getColumnNameValue($action);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkSocialActivity($lab_id, $column, $action)
    {
        try {
            return $this->labSocialActivitiesService->checkSocialActivity($lab_id, $column, $action);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function captureSocialActivity($lab_id, $column, $value)
    {
        try {
            return $this->labSocialActivitiesService->captureSocialActivity($lab_id, $column, $value);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function joinLab($lab,$component,$request,$memberList){
        try {
            return $this->memberManagementService->addMembers($lab,$component,$request,$memberList);
        }catch (\Exception $e){
            return false;
        }
    }
    public function unJoinLab($lab,$component,$request){
        try {
            return $this->memberManagementService->deleteMembers($lab,$component,$request);
        }catch (\Exception $e){
            return false;
        }
    }

    public function checkJoinedOrNot($lab,$component){
        try {
            return $this->memberManagementService->checkJoinedOrNot($lab,$component);
        } catch (\Exception $e) {
            return false;
        }
    }

    public  function getRecordsFromJoinRequestArray(){
        try {
            return $this->memberManagementService->getRecordsFromJoinRequestArray();
        } catch (\Exception $e) {
            return false;
        }
    }

    public  function getRecordsFromJoinRequestObject(){
        try {
            return $this->memberManagementService->getRecordsFromJoinRequestObject();
        } catch (\Exception $e) {
            return false;
        }
    }
}
