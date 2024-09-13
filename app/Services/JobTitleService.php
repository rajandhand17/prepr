<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\JobTitle;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class JobTitleService
{
    public static function getJobTitles($language = 'en', $search = null, $job_title_id = null, $sortBy = null, $pagination = null)
    {
        try {
            if ($language == 'en') {
                $job_list = JobTitle::select('id', 'title', 'uuid', 'created_at');
                if ($job_title_id !== null) {
                    $job_list = $job_list->whereIn('id', $job_title_id);
                }
            } else {
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                if (!$column_name || !Schema::hasColumn('jobs', $column_name)) {
                    return false;
                }
                $job_list = JobTitle::select('id', $column_name.' as title', 'uuid', 'created_at');
            }
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $job_list = self::filterJobList($job_list, $column_name, $search);
            }

            if ($sortBy !== null) {
                switch ($sortBy) {
                    case 'name-a-to-z':
                        $job_list = $job_list->orderBy('job_titles.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $job_list = $job_list->orderBy('job_titles.title', 'DESC');
                        break;
                    case 'creation_date':
                        $job_list = $job_list->orderBy('job_titles.created_at', 'ASC');
                        break;
                    default:
                        $job_list = $job_list->orderBy('job_titles.id', 'ASC');
                }
            }
            $job_list = $job_list->take(config('site-settings.dropdown_listing_limit'));

            if (auth()->user() && $pagination == null) {
                $job_list = $job_list->paginate(config('site-settings.pagination_per_page_career'));
            } else {
                $job_list = $job_list->get();
            }

            return $job_list;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in getJobTitles in JobTitleService.php: '.$e->getMessage());

            return false;
        }
    }

    public static function filterJobList($getJobTitlesList, $job_column_name, $search)
    {
        try {
            $getJobTitlesList = $getJobTitlesList->where($job_column_name, 'like', '%'.$search.'%');

            return $getJobTitlesList;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in filterJobList in JobTitleService.php: '.$e->getMessage());

            return false;
        }
    }

    public static function getJobBasedOnIdArray($job_title_ids)
    {
        try {
            $getJobsList = JobTitle::select('id', LanguageColumnHelper::getLanguageColumnName(app()->getLocale(), 'title').' as title')
                ->whereIn('id', $job_title_ids)->get();

            return $getJobsList;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in getJobBasedOnIdArray in JobTitleService.php: '.$e->getMessage());

            return false;
        }
    }

    public static function getJobBasedOnId($job_title_id)
    {
        try {
            $getJobsList = JobTitle::select('id', LanguageColumnHelper::getLanguageColumnName(app()->getLocale(), 'title').' as title')
                ->where('id', $job_title_id)->first();

            return $getJobsList;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in getJobBasedOnId in JobTitleService.php: '.$e->getMessage());

            return false;
        }
    }

    public static function getJobsBasedOnUsers($request)
    {
        try {
            $getUsersJobsIds = UserJobTitlesService::getUsersJobs($request->pinned);
            $getJobs = null;
            if ($getUsersJobsIds !== false) {
                $getJobs = self::getJobTitles($request->language, $request->search, $getUsersJobsIds, $request->sort_by, 'yes');
            }

            return $getJobs;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getRelatedCareer($request)
    {
        try {
            $getCurrentUsersSkills = UserSkillsService::getUserSkills();
            $getJobsIdsBasedOnSkills = JobTitleSkillServices::getJobTitleBasedOnSkills($getCurrentUsersSkills);
            $getCurrentUsersJobs = UserJobTitlesService::getUsersJobs();
            $getJobIds = $getJobsIdsBasedOnSkills->merge($getCurrentUsersJobs)->unique();
            if ($request->saved !== null) {
                $getJobIds = UserJobTitlesService::getUserJob($getJobIds, $request->saved);
            }
            $getJobIds = $getJobIds->all();
            $getJobIds = array_slice($getJobIds, 0, 100);
            $getJobTitle = self::getJobTitles($request->language, $request->search, $getJobIds, $request->sort_by);

            return $getJobTitle ?: false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getJobDetails($id)
    {
        try {
            $getJobDetails = JobTitle::where('id', $id)->first();

            return $getJobDetails;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getRelatedJobs($id)
    {
        try {
            $getJobSkills = JobTitleSkillServices::getJobSkillsBasedOnJobId($id);
            $getJobsBasedOnSkills = JobTitleSkillServices::getJobTitleBasedOnSkills($getJobSkills, $id)->all();
            $getJobIds = array_slice($getJobsBasedOnSkills, 0, 3);
            $getJobDetails = JobTitle::whereIn('id', $getJobIds)->get();

            return $getJobDetails;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getJobBasedOnIds($job_ids)
    {
        try {
            $getJobsList = JobTitle::select('id', LanguageColumnHelper::getLanguageColumnName(app()->getLocale(), 'title').' as title')
                ->whereIn('id', $job_ids)->get();
            if ($getJobsList) {
                return $getJobsList;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function gettrendingJobs($job)
    {
        try {
            // call lightcast api for job trends
            $data = Http::post('https://lightcast.io/api/jpa/job-postings-trend', [
                'skillId'      => null,
                'titleId'      => $job->lc_id,
                'occupationId' => null,
                'country'      => 'us',
            ]);
            $data = json_decode($data, true);

            $labelArray = [];
            $currArray = [];

            // Define an array of month names
            $monthNames = [
                '01' => 'Jan',
                '02' => 'Feb',
                '03' => 'Mar',
                '04' => 'Apr',
                '05' => 'May',
                '06' => 'Jun',
                '07' => 'Jul',
                '08' => 'Aug',
                '09' => 'Sep',
                '10' => 'Oct',
                '11' => 'Nov',
                '12' => 'Dec',
            ];
            foreach ($data as $item) {
                $yearMonth = explode('-', $item['label']);
                $year = $yearMonth[0];
                $month = $yearMonth[1];

                $shortMonth = $monthNames[$month];

                $formattedLabel = $shortMonth.' '.$year;
                $currArray[] = [
                    'date'   => $formattedLabel,
                    'trends' => $item['curr'],
                ];
            }

            return $currArray;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLiveJobs($job)
    {
        try {
            $postData = [
                'skillId'      => null,
                'titleId'      => $job->lc_id,
                'occupationId' => null,
                'country'      => 'ca',
            ];
            // Making the POST request
            $response = Http::post('https://lightcast.io/api/jpa/live-job-postings', $postData);
            // Decoding the JSON response
            $responseBody = json_decode($response->body(), true);
            // Process the response and format it
            $jobPostings = ['jobPostings' => []];
            foreach ($responseBody as $jobPosting) {
                $postedDate = Carbon::createFromFormat('Y-m-d', $jobPosting['posted']);
                $datePosted = $postedDate->diffForHumans();

                $jobPostings['jobPostings'][] = [
                    'name'       => $jobPosting['title_raw'],
                    'company'    => $jobPosting['company_name'],
                    'location'   => $jobPosting['city_name'],
                    'datePosted' => $datePosted,
                    'url'        => $jobPosting['url'][0] ?? null,
                ];
            }

            return $jobPostings;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
