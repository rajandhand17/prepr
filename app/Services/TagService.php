<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
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
            return false;
        }
    }
}
