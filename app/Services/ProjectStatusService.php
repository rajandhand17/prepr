<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\ProjectStatus;
use Illuminate\Support\Facades\Schema;

class ProjectStatusService
{
    public function getProjectStatus($language = 'en', $search = null)
    {
        try {
            if ($language == 'en') {
                $project_status_list = ProjectStatus::select('id', 'title');
            //Search categories based on user input
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('project_status', $column_name)) {
                    return false;
                }
                $project_status_list = ProjectStatus::select('id', $column_name.' as title');
            }

            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $project_status_list = $project_status_list->where($column_name, 'like', '%'.$search.'%');
            }

            //take 20 results based from the table
            $project_status_list = $project_status_list->take(config('site-settings.dropdown_listing_limit'))->get();

            //check if there are any results
            if (!$project_status_list->isEmpty()) {
                return $project_status_list;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
