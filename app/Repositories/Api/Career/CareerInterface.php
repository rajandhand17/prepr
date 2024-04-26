<?php

namespace App\Repositories\Api\Career;

interface CareerInterface
{
    public function getMyJobsListing($request);

    public function checkJobsExistsInUsers($job_id);

    public function addJobs($request);

    public function addJobPinned($request);

    public function checkJobExistsOrNot($jobId);

    public function deleteJob($jobId);

    public function getRelatedCareer($request);

    public function getJobDetails($id);
}
