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
            return $this->labSocialActivitiesService->checkSocialActivity($lab_id,$column,$action);
        } catch (\Exception $e) {
            return false;
        }
    }
    public function captureSocialActivity($lab_id,$column,$value)
    {
        try {
            return $this->labSocialActivitiesService->captureSocialActivity($lab_id,$column,$value);
        }catch (\Exception $e) {
            return false;
        }
    }
}
