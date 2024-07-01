<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\ProjectSubmissionRequirement;
use Exception;
use Illuminate\Support\Facades\Schema;

class ProjectSubmissionRequirementService
{
    public function getProjectSubmissionRequirements($language = 'en', $search = null)
    {
        try {
            if ($language == 'en') {
                $project_submission_requirements = ProjectSubmissionRequirement::select('id', 'title', 'status');
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');
                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('project_submission_requirements', $column_name)) {
                    return false;
                }
                $project_submission_requirements = ProjectSubmissionRequirement::select('id', $column_name.' as title', 'status');
            }
            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $project_submission_requirements = $project_submission_requirements->where($column_name, 'like', '%'.$search.'%');
            }

            //take 20 results based from the table
            $project_submission_requirements = $project_submission_requirements->take(config('site-settings.dropdown_listing_limit'))->get();

            //check if there are any results
            if (!$project_submission_requirements->isEmpty()) {
                return $project_submission_requirements;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getProjectSubmissionRequirementByID($language = 'en', $project_condition_id)
    {
        try {
            if ($language == 'en') {
                $project_submission_requirement = ProjectSubmissionRequirement::select('id', 'title');
            } else {
                //get column title based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('project_submission_requirements', $column_name)) {
                    return false;
                }
                $project_submission_requirement = ProjectSubmissionRequirement::select('id', $column_name.' as title');
            }
            $project_condition = $project_submission_requirement->find($project_condition_id);

            return $project_condition;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
