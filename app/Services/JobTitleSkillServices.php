<?php

namespace App\Services;

use App\Models\JobTitleSkill;

class JobTitleSkillServices
{
    public static function getJobTitleBasedOnSkills($skills,$jobId=null)
    {
        try {
            $getJobTitleBasedOnSkills = JobTitleSkill::whereIn('skill_id', $skills);
            if($jobId!==null){
                $getJobTitleBasedOnSkills = $getJobTitleBasedOnSkills->where('job_title_id', '!=', $jobId);

            }
            $getJobTitleBasedOnSkills=$getJobTitleBasedOnSkills->distinct()->pluck('job_title_id');
            if ($getJobTitleBasedOnSkills) {
                return $getJobTitleBasedOnSkills;
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

    public static function getPercentagesOfMatchedSkills($jobId)
    {
        try {
            $usersSkills = UserSkillsService::getUserSkills();
            $requiredSKills = self::getJobSkillsBasedOnJobId($jobId);
            $commonSkills = $usersSkills->intersect($requiredSKills);
            $getCountOfRequiredSkills = $requiredSKills->count();
            $countOfMatchedSkills = $commonSkills->count();
            $percentage = ($countOfMatchedSkills / $getCountOfRequiredSkills) * 100;

            return $percentage;
        } catch (\Exception $e) {
            return false;
        }
    }
}
