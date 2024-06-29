<?php

namespace App\Repositories\Api\Career;

use App\Helpers\UtilityHelper;
use App\Services\JobTitleService;
use App\Services\UserJobTitlesService;

class CareerRepository implements CareerInterface
{
    private $jobTitleService;

    private $userJobTitleService;

    public function __construct(JobTitleService $jobTitleService, UserJobTitlesService $userJobTitleService)
    {
        $this->jobTitleService = $jobTitleService;
        $this->userJobTitleService = $userJobTitleService;
    }

    public function getMyJobsListing($request)
    {
        try {
            return $this->jobTitleService->getJobsBasedOnUsers($request);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function checkJobsExistsInUsers($job_id)
    {
        try {
            return $this->userJobTitleService->checkJobsExistsInUsers($job_id);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function addJobs($request)
    {
        try {
            return $this->userJobTitleService->addJobs($request);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function addJobPinned($request)
    {
        try {
            return $this->userJobTitleService->addJobPinned($request);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function checkJobExistsOrNot($jobId)
    {
        try {
            return $this->userJobTitleService->checkJobExistsOrNot($jobId);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function deleteJob($jobId)
    {
        try {
            return $this->userJobTitleService->deleteJob($jobId);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getRelatedCareer($request)
    {
        try {
            return $this->jobTitleService->getRelatedCareer($request);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getJobDetails($id)
    {
        try {
            return $this->jobTitleService->getJobDetails($id);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getJobsDetails($ids)
    {
        try {
            return $this->jobTitleService->getJobBasedOnIds($ids);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function addMultipleJobs($request)
    {
        try {
            return $this->userJobTitleService->addMultipleJobs($request);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
