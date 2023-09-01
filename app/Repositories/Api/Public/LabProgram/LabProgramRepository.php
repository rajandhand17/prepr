<?php

namespace App\Repositories\Api\Public\LabProgram;

use App\Services\Public\LabProgramService;
use App\Services\Public\LabProgramSocialActivitiesService;
use App\Services\Public\LabService;
use App\Services\Public\LabSocialActivitiesService;
use App\Services\Public\MemberManagementService;

class LabProgramRepository implements LabProgramInterface
{
    private $labProgramService;
    private $labProgramSocialActivitiesService;
    private $memberManagementService;

    public function __construct(LabProgramService $labProgramService, LabProgramSocialActivitiesService $labProgramSocialActivitiesService, MemberManagementService $memberManagementService)
    {
        $this->labProgramService = $labProgramService;
        $this->labProgramSocialActivitiesService = $labProgramSocialActivitiesService;
        $this->memberManagementService = $memberManagementService;
    }

    public function getList($request)
    {
        try {
            return $this->labProgramService->getList($request);
        } catch (\Exception $e) {
            dd($e);
            return false;
        }
    }

    public function getLabProgramBasedOnSlug($slug)
    {
        try {
            return $this->labProgramService->getLabProgramBasedOnSlug($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getColumnNameValue($action)
    {
        try {
            return $this->labProgramSocialActivitiesService->getColumnNameValue($action);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkSocialActivity($labProgram, $column, $action)
    {
        try {
            return $this->labProgramSocialActivitiesService->checkSocialActivity($labProgram, $column, $action);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function captureSocialActivity($labProgram, $column, $value)
    {
        try {
            return $this->labProgramSocialActivitiesService->captureSocialActivity($labProgram, $column, $value);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function joinLabProgram($lab, $component, $request, $memberList)
    {
        try {
            return $this->memberManagementService->addMembers($lab, $component, $request, $memberList);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function unjoinLabProgram($lab, $component, $request)
    {
        try {
            return $this->memberManagementService->deleteMembers($lab, $component, $request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkJoinedOrNot($lab, $component)
    {
        try {
            return $this->memberManagementService->checkJoinedOrNot($lab, $component);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getRecordsFromJoinRequest()
    {
        try {
            return $this->memberManagementService->getRecordsFromJoinRequest();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function setJoinRequestParameters($language)
    {
        try {
            return $this->memberManagementService->setJoinRequestParameters($language);
        } catch (\Exception $e) {
            return false;
        }
    }
}
