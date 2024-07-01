<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\Tag;

class TagService
{
    public static function getTagsBasedOnIds($tag_ids)
    {
        try {
            $getTagList = Tag::select('id', LanguageColumnHelper::getLanguageColumnName(app()->getLocale(), 'title').' as title')
                ->whereIn('id', $tag_ids)->get();
            if ($getTagList) {
                return $getTagList;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    //for fetch the records with filter and without filter also
    public function getTags($language = 'en', $search = null)
    {
        try {
            if ($language == 'en') {
                $tag_list = Tag::select('id', 'title', 'tag_image', 'components');
            //Search categories based on user input
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('tags', $column_name)) {
                    return false;
                }
                //get image column name based on language
                $image_column = LanguageColumnHelper::getLanguageColumnName($language, 'tag_image');

                if (!$image_column || !Schema::hasColumn('tags', $image_column)) {
                    return false;
                }

                $tag_list = Tag::select('id', $column_name.' as title', $image_column.' as tag_image');
            }

            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $tag_list = $tag_list->where($column_name, 'like', '%'.$search.'%');
            }

            //take 20 results based from the table
            $tag_list = $tag_list->take(config('site-settings.dropdown_listing_limit'))->get();

            //check if there are any results
            if (!$tag_list->isEmpty()) {
                return $tag_list;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getTagsIdBasedOnId($resourceGroupTagId)
    {
        try {
            $getTagsList = Tag::select('id', LanguageColumnHelper::getLanguageColumnName(app()->getLocale(), 'title').' as title')
                ->where('id', $resourceGroupTagId)->get();
            if ($getTagsList) {
                return $getTagsList;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
