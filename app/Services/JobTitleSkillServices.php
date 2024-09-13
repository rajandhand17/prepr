<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\JobTitleSkill;

class JobTitleSkillServices
{
    public static function getJobTitleBasedOnSkills($skills, $jobId = null)
    {
        try {
            $getJobTitleBasedOnSkills = JobTitleSkill::whereIn('skill_id', $skills);
            if ($jobId !== null) {
                $getJobTitleBasedOnSkills = $getJobTitleBasedOnSkills->where('job_title_id', '!=', $jobId);
            }
            $getJobTitleBasedOnSkills = $getJobTitleBasedOnSkills->distinct()->pluck('job_title_id');
            if ($getJobTitleBasedOnSkills) {
                return $getJobTitleBasedOnSkills;
            }

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getPercentagesOfMatchedSkills($jobId)
    {
        try {
            $usersSkills = UserSkillsService::getUserSkills();
            $requiredSKills = self::getJobSkillsBasedOnJobId($jobId);
            $countOfMatchedSkills = 0;
            if (isset(auth()->user()->id)) {
                $commonSkills = $usersSkills->intersect($requiredSKills);
                $countOfMatchedSkills = $commonSkills->count();
            }
            $getCountOfRequiredSkills = $requiredSKills->count();

            $percentage = ($countOfMatchedSkills / $getCountOfRequiredSkills) * 100;

            return $percentage;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
