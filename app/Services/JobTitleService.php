<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Models\JobTitle;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class JobTitleService
{
    public static function getJobsTitles($language = 'en', $search = null, $job_id = null)
    {
        try {
            if ($language == 'en') {
                $job_list = JobTitle::select('id', 'title');
                if ($job_id !== null) {
                    $job_list = $job_list->whereIn('id', $job_id);
                }
            } else {
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                if (!$column_name || !Schema::hasColumn('jobs', $column_name)) {
                    return false;
                }
                $job_list = JobTitle::select('id', $column_name.' as title');
            }

            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $job_list = self::filterJobList($job_list, $column_name, $search);
            }

            $job_list = $job_list->take(config('site-settings.dropdown_listing_limit'));

            if (auth()->user()) {
                $job_list = $job_list->paginate(config('site-settings.pagination_per_page'));
            } else {
                $job_list = $job_list->get();
            }

            return $job_list;
        } catch (Exception $e) {
            Log::error('Error in getJobsTitles in JobTitleService.php: '.$e->getMessage());

            return false;
        }
    }

    public static function filterJobList($getJobTitlesList, $job_column_name, $search)
    {
        try {
            $getJobTitlesList = $getJobTitlesList->where($job_column_name, 'like', '%'.$search.'%');

            return $getJobTitlesList;
        } catch (Exception $e) {
            Log::error('Error in filterJobList in JobTitleService.php: '.$e->getMessage());

            return false;
        }
    }

    public static function getJobBasedOnIdArray($job_ids)
    {
        try {
            $getJobsList = JobTitle::select('id', LanguageColumnHelper::getLanguageColumnName(app()->getLocale(), 'title').' as title')
                ->whereIn('id', $job_ids)->get();

            return $getJobsList;
        } catch (Exception $e) {
            Log::error('Error in getJobBasedOnIdArray in JobTitleService.php: '.$e->getMessage());

            return false;
        }
    }

    public static function getJobBasedOnId($job_id)
    {
        try {
            $getJobsList = JobTitle::select('id', LanguageColumnHelper::getLanguageColumnName(app()->getLocale(), 'title').' as title')
                ->where('id', $job_id)->first();

            return $getJobsList;
        } catch (Exception $e) {
            Log::error('Error in getJobBasedOnId in JobTitleService.php: '.$e->getMessage());

            return false;
        }
    }
}
