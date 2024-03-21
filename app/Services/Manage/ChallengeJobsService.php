<?php

namespace App\Services\Manage;

use App\Models\ChallengeJobTitles;
use Exception;
use Illuminate\Support\Facades\Log;

class ChallengeJobsService
{
    public function createChallengeJobs($request, $challenge_id)
    {
        try {
            if ($request->has('jobs')) {
                if (count($request->jobs) > 0) {
                    foreach ($request->jobs as $job) {
                        $ChallengeJobsGroupsStack = new ChallengeJobTitles();
                        $ChallengeJobsGroupsStack->challenge_id = $challenge_id;
                        $ChallengeJobsGroupsStack->job_title_id = $job;
                        $ChallengeJobsGroupsStack->save();
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error('Error in createChallengeJobs in ChallengeJobs.php: '.$e->getMessage());

            return false;
        }
    }
}
