<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class CategoryService
{
    public function getCategories($language = 'en', $search = null, $component = null)
    {
        try {
            if ($language == 'en') {
                $category_list = Category::select('id', 'title', 'parent_id', 'components');
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');
                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('categories', $column_name)) {
                    return false;
                }
                $category_list = Category::select('id', $column_name.' as title', 'parent_id', 'components');
            }

            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $category_list = $category_list->where($column_name, 'like', '%'.$search.'%');
            }

            //get categories based on component
            if ($component != null) {
                $category_list = $category_list->where('components', 'like', '%'.$component.'%');
            }

            //take 20 results based from the table
            $category_list = $category_list->take(config('site-settings.dropdown_listing_limit'))->get();

            //check if there are any results
            if (!$category_list->isEmpty()) {
                return $category_list;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getCategoryBasedOnId($category_id)
    {
        try {
            $fetchCategory = Category::where('id', $category_id)->first();

            return  $fetchCategory;
        } catch (\Exception $e) {
            return false;
        }
    }
}
