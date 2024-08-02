<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;

class LabTagsGroupsService
{
    public static function createCloneLabTagsGroups($originalLabsTags, $clonedLabId)
    {
        try {
            $originalLabsTags->each(function ($tags) use ($clonedLabId) {
                if ($tags) {
                    $cloneTag = $tags->replicate();
                    $cloneTag->lab_id = $clonedLabId;
                    $cloneTag->save();
                }
            });

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
