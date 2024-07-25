<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ResourceGroupTypeModes;

class ResourceGroupTypeModesService
{
    public static function getResourceGroupBasedOnType($type)
    {
        try {
            // Type 0 belongs to type and type 1 belongs to mode
            return ResourceGroupTypeModes::where(['type_mode'=>config('constants.resource_mode.type'), 'value'=>config('constants.resource_types.'.$type)])->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
