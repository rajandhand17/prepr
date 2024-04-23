<?php

namespace App\Services;

use App\Models\UserJobTitle;

class UserJobTitlesService
{
    public static function getUsersJobs()
    {
        try {
            $getCurrentUsersJobs = UserJobTitle::where('user_id', auth()->user()->id)->pluck('job_title_id')->unique();
            if (!empty($getCurrentUsersJobs)) {
                return $getCurrentUsersJobs;
            }

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function addJobs($request)
    {
        try {
            $addedJobs = new UserJobTitle();
            $addedJobs->user_id = auth()->user()->id;
            $addedJobs->job_title_id = $request->job_title_id;
            if ($addedJobs->save()) {
                return true;
            }

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function checkJobsExistsInUsers($jobId)
    {
        try {
            $getJobs = UserJobTitle::where([
                'user_id'     => auth()->user()->id,
                'job_title_id'=> $jobId,
            ])->first();
            if ($getJobs) {
                return $getJobs;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function addJobPinned($jobId)
    {
        try {
            $userJobTitle = UserJobTitle::where(['user_id'=>auth()->user()->id, 'job_title_id'=>$jobId])->first();
            if ($userJobTitle) {
                $userJobTitle->pinned = '1';
                $userJobTitle->save();
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function deleteJob($jobId)
    {
        try {
            $job = UserJobTitle::where('id', $jobId)->first();
            if ($job) {
                UserJobTitle::where('id', $jobId)->delete();

                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
