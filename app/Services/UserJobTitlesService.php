<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\UserJobTitle;

class UserJobTitlesService
{
    public static function getUsersJobs($pinned = null)
    {
        try {
            $pin = ($pinned == 'yes') ? '1' : '0';
            $getCurrentUsersJobs = UserJobTitle::where('user_id', auth()->user()->id);
            if ($pinned !== null) {
                $getCurrentUsersJobs = $getCurrentUsersJobs->where('pinned', $pin);
            }
            $getCurrentUsersJobs = $getCurrentUsersJobs->distinct()->pluck('job_title_id');
            if (!empty($getCurrentUsersJobs)) {
                return $getCurrentUsersJobs;
            }

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function addJobs($request)
    {
        try {
            $getAllSkillsOfJobs = JobTitleSkillServices::getJobSkillsBasedOnJobId($request->job_id);
            $addedUsersInSkills = UserSkillsService::addMultipleSkills($getAllSkillsOfJobs);
            if ($addedUsersInSkills) {
                $addedJobs = new UserJobTitle();
                $addedJobs->user_id = auth()->user()->id;
                $addedJobs->job_title_id = $request->job_id;
                if ($addedJobs->save()) {
                    return true;
                }
            }

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function addMultipleJobs($request)
    {
        try {
            $jobIds = $request->job_ids; // Assuming job_ids is an array of job IDs
            $already = [];
            $error = [];
            $success = [];
            foreach ($jobIds as $jobId) {
                $checkJobExistsOrNot = self::checkJobsExistsInUsers($jobId);
                if ($checkJobExistsOrNot) {
                    $already[]['id'] = $jobId;
                    continue;
                }
                $getAllSkillsOfJobs = JobTitleSkillServices::getJobSkillsBasedOnJobId($jobId);
                $addedUsersInSkills = UserSkillsService::addMultipleSkills($getAllSkillsOfJobs);

                if ($addedUsersInSkills) {
                    $addedJobs = new UserJobTitle();
                    $addedJobs->user_id = auth()->user()->id;
                    $addedJobs->job_title_id = $jobId;

                    if (!$addedJobs->save()) {
                        $error[] = "Failed to save job with ID: $jobId";
                    } else {
                        $success[]['id'] = $jobId;
                    }
                } else {
                    $error[] = "Failed to add skills for job with ID: $jobId";
                }
            }
            $responses = [
                'succeeded'        => $success,
                'already_added_ids'=> $already,
                'error'            => $error,
            ];

            return $responses;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function addJobPinned($request)
    {
        try {
            $pinned = $request->pinned == 'yes' ? '1' : '0';
            $userJobTitle = UserJobTitle::where(['user_id'=>auth()->user()->id, 'job_title_id'=>$request->job_id])->first();
            if ($userJobTitle) {
                $userJobTitle->pinned = $pinned;
                $userJobTitle->save();
            } else {
                $userJobTitle = UserJobTitle::create([
                    'user_id'     => auth()->user()->id,
                    'job_title_id'=> $request->job_id,
                    'pinned'      => $pinned,
                ]);
            }

            return $userJobTitle;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteJob($jobId)
    {
        try {
            $job = UserJobTitle::where(['user_id'=>auth()->user()->id, 'job_title_id'=>$jobId])->first();
            if ($job) {
                UserJobTitle::where(['user_id'=>auth()->user()->id, 'job_title_id'=>$jobId])->delete();

                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkJobExistsOrNot($jobId)
    {
        try {
            $job = UserJobTitle::where(['user_id'=>auth()->user()->id, 'job_title_id'=>$jobId])->first();
            if ($job) {
                return $job;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getUserJob($jobIds, $save = null)
    {
        try {
            $userJobIds = UserJobTitle::where('user_id', auth()->user()->id)
                ->pluck('job_title_id')->all();
            $collection = collect($jobIds);
            $filtered = $collection->filter(function ($item) use ($userJobIds, $save) {
                return $save == 'yes' ? in_array($item, $userJobIds) : !in_array($item, $userJobIds);
            });
            return $filtered->values();
        } catch (\Exception $e) {
            return false;
        }
    }
}
