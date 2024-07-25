<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ResourceCollectionTypeModes;

class ResourceCollectionTypeModesService
{
    public static function getResourceCollectionBasedOnType($type)
    {
        try {
            // Type 0 belongs to type and type 1 belongs to mode
            return ResourceCollectionTypeModes::where(['type_mode'=>config('constants.resource_mode.type'), 'value'=>config('constants.resource_types.'.$type)])->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
