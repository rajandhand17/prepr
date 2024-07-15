<?php

namespace App\Services\Maestro;

use App\Models\LabTagsGroups;

class LabTagsGroupsService
{
    public static function createLabTagsGroups($originalLabsTags, $clonedLabId)
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
            return false;
        }
    }
}
