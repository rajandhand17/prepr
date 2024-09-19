<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\ProjectIndustry;
use Illuminate\Support\Facades\Schema;

class ProjectIndustryService
{
    public function getProjectIndustries($language = 'en', $search = null)
    {
        try {
            if ($language == 'en') {
                $project_industry_list = ProjectIndustry::select('id', 'title')->where('status','1');
            //Search categories based on user input
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('skills', $column_name)) {
                    return false;
                }
                $project_industry_list = ProjectIndustry::select('id', $column_name.' as title')->where('status','1');
            }

            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $project_industry_list = $project_industry_list->where($column_name, 'like', '%'.$search.'%');
            }

            //take 20 results based from the table
            $project_industry_list = $project_industry_list->take(config('site-settings.dropdown_listing_limit'))->get();

            //check if there are any results
            if (!$project_industry_list->isEmpty()) {
                return $project_industry_list;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
