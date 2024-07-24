<?php

namespace App\Services\Public;

use App\Models\ResourceModuleTypeModes;
use function Symfony\Component\Translation\t;

class ResourceModuleTypeModesService
{
    public static function getResourceModuleBasedOnType($type)
    {
        try {
            // Type 0 belongs to type and type 1 belongs to mode
            return ResourceModuleTypeModes::where(['type_mode'=>'0','value'=>$type])->get();
        }catch (\Exception $e) {
            return false;
        }
    }
}
