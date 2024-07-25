<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\ResourceModuleTypeModes;

class ResourceModuleTypeModesService
{

    public static function getResourceModuleBasedOnType($type)
    {
        try {
            // Type 0 belongs to type and type 1 belongs to mode
            return ResourceModuleTypeModes::where(['type_mode'=>config('constants.resource_mode.type'),'value'=>config('constants.resource_types.'.$type)])->get();
        }catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
