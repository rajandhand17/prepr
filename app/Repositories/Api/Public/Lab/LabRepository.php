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

    public function getLabList($request)
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

    public function checkLabActivity($action, $lab_id)
    {
        try {
            return $this->labSocialActivitiesService->checkLabActivity($action, $lab_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function joinLab($lab_id)
    {
        try {
            return $this->labSocialActivitiesService->joinLab($lab_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function unjoinLab($lab_id)
    {
        try {
            return $this->labSocialActivitiesService->unjoinLab($lab_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function followLab($lab_id)
    {
        try {
            return $this->labSocialActivitiesService->followLab($lab_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function unfollowLab($lab_id)
    {
        try {
            return $this->labSocialActivitiesService->unfollowLab($lab_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function share($lab_id)
    {
        try {
            return $this->labSocialActivitiesService->share($lab_id);
        } catch (\Exception $e) {
            return false;
        }
    }
}
