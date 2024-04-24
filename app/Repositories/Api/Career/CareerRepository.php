<?php

namespace App\Repositories\Api\Career;

use App\Services\JobTitleService;
use App\Services\RelatedJobTitleService;
use App\Services\UserJobTitlesService;

class CareerRepository implements CareerInterface
{
    private $jobTitleService;

    private $userJobTitleService;

    private $relatedJobTitleService;

    public function __construct(RelatedJobTitleService $relatedJobTitleService, JobTitleService $jobTitleService, UserJobTitlesService $userJobTitleService)
    {
        $this->jobTitleService = $jobTitleService;
        $this->userJobTitleService = $userJobTitleService;
        $this->relatedJobTitleService = $relatedJobTitleService;
    }

    public function getMyJobsListing($request)
    {
        try {
            return $this->jobTitleService->getJobsBasedOnUsers($request);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function addJobs($request)
    {
        try {
            return $this->userJobTitleService->addJobs($request);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function addJobPinned($request)
    {
        try {
            return $this->userJobTitleService->addJobPinned($request);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function deleteJob($jobId)
    {
        try {
            return $this->userJobTitleService->deleteJob($jobId);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getRelatedCareer()
    {
        try {
            return $this->jobTitleService->getRelatedCareer();
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getJobDetails($id)
    {
        try {
            return $this->jobTitleService->getJobDetails($id);
        } catch(\Exception $e) {
            return false;
        }
    }
}
