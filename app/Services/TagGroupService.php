<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Models\TagGroup;

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
            return false;
        }
    }
}
