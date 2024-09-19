<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\ProjectType;
use Illuminate\Support\Facades\Schema;

class ProjectTypeService
{
    public function getProjectTypes($language = 'en', $search = null)
    {
        try {
            if ($language == 'en') {
                $project_type_list = ProjectType::select('id', 'title')->where('status', '1');
            //Search categories based on user input
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('project_types', $column_name)) {
                    return false;
                }
                $project_type_list = ProjectType::select('id', $column_name.' as title')->where('status', '1');
            }

            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $project_type_list = $project_type_list->where($column_name, 'like', '%'.$search.'%');
            }

            //take 20 results based from the table
            $project_type_list = $project_type_list->take(config('site-settings.dropdown_listing_limit'))->get();

            //check if there are any results
            if (!$project_type_list->isEmpty()) {
                return $project_type_list;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
