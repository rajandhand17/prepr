<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\TagGroup;
use Illuminate\Support\Facades\Schema;

class TagGroupService
{
    public static function getTagGroupsBasedOnIds($tag_group_ids)
    {
        try {
            $getTagGroupList = TagGroup::select('id', LanguageColumnHelper::getLanguageColumnName(app()->getLocale(), 'title').' as title')
                ->whereIn('id', $tag_group_ids)->get();
            if ($getTagGroupList) {
                return $getTagGroupList;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getTagGroups($language = 'en', $search = null, $tags = null)
    {
        try {
            if ($language == 'en') {
                $tag_group = TagGroup::select('id', 'title', 'tags', 'description');
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                //check whether the column exist in the db or not
                if (!$column_name || Schema::hasColumn('taggroups', $column_name)) {
                    return false;
                }

                $tag_group = TagGroup::select('id', $column_name.' as title', 'tags', 'description');
            }

            //Search tag name based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $tag_group = $tag_group->where($column_name, 'like', '%'.$search.'%');
            }

            //Search tag based on used input
            if ($tags != null) {
                $tag_group = $tag_group->where('tags', 'like', '%'.$tags.'%');
            }

            //take 20 results based from the table
            $tag_group = $tag_group->take(config('site-settings.dropdown_listing_limit'))->get();

            //check if there are any results
            if (!$tag_group->isEmpty()) {
                return $tag_group;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
