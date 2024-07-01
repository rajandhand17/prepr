<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\ProjectStage;
use Illuminate\Support\Facades\Schema;

class ProjectStageService
{
    public function getProjectStages($language = 'en', $search = null)
    {
        try {
            if ($language == 'en') {
                $project_stage_list = ProjectStage::select('id', 'title');
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('project_stages', $column_name)) {
                    return false;
                }
                $project_stage_list = ProjectStage::select('id', $column_name.' as title');
            }

            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $project_stage_list = $project_stage_list->where($column_name, 'like', '%'.$search.'%');
            }

            //take 20 results based from the table
            $project_stage_list = $project_stage_list->take(config('site-settings.dropdown_listing_limit'))->get();

            //check if there are any results
            if (!$project_stage_list->isEmpty()) {
                return $project_stage_list;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
