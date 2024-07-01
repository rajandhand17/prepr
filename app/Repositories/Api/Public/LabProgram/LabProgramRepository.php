<?php

namespace App\Repositories\Api\Public\LabProgram;

use App\Helpers\UtilityHelper;
use App\Services\Public\LabProgramService;
use App\Services\Public\LabProgramSocialActivitiesService;

class LabProgramRepository implements LabProgramInterface
{
    private $labProgramService;
    private $labProgramSocialActivitiesService;

    public function __construct(LabProgramService $labProgramService, LabProgramSocialActivitiesService $labProgramSocialActivitiesService)
    {
        $this->labProgramService = $labProgramService;
        $this->labProgramSocialActivitiesService = $labProgramSocialActivitiesService;
    }

    public function getList($request)
    {
        try {
            return $this->labProgramService->getList($request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getLabProgramBasedOnSlug($slug)
    {
        try {
            return $this->labProgramService->getLabProgramBasedOnSlug($slug);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getColumnNameValue($action)
    {
        try {
            return $this->labProgramSocialActivitiesService->getColumnNameValue($action);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkSocialActivity($labProgram, $column, $action)
    {
        try {
            return $this->labProgramSocialActivitiesService->checkSocialActivity($labProgram, $column, $action);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function captureSocialActivity($labProgram, $column, $value)
    {
        try {
            return $this->labProgramSocialActivitiesService->captureSocialActivity($labProgram, $column, $value);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
