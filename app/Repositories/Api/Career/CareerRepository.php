<?php

namespace App\Repositories\Api\Career;

use App\Services\JobTitleService;
use App\Services\UserJobTitlesService;

class CareerRepository implements CareerInterface
{
    private $jobTitleService;

    private $userJobTitleService;
    public function __construct(JobTitleService $jobTitleService,UserJobTitlesService $userJobTitleService)
    {
        $this->jobTitleService = $jobTitleService;
        $this->userJobTitleService=$userJobTitleService;
    }

    public function getMyJobsListing($request){
        try {
            return $this->jobTitleService->getJobsBasedOnUsers($request);
        }catch(\Exception $e){
            return false;
        }
    }
}
