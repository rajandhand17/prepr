<?php

namespace App\Services;

use App\Models\JobTitleSkill;

class JobTitleSkillServices
{
    public static function getJobTitleBasedOnSkills($skills)
    {
        try {
            $getJobTitleBasedOnSKills = JobTitleSkill::whereIn('skill_id', $skills)->pluck('job_title_id')->unique()->toArray();
            if ($getJobTitleBasedOnSKills) {
                return $getJobTitleBasedOnSKills;
            }

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function getJobSkillsBasedOnJobId($jobId)
    {
        try {
            $getJobSKills = JobTitleSkill::where('job_title_id', $jobId)->pluck('skill_id')->unique();
            if ($getJobSKills) {
                return $getJobSKills;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
