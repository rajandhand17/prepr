<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChannelApis;

class ChannelApiService
{
    public static function getChannelApiByName($name)
    {
        try {
            return ChannelApis::query()->where(['api_slug' => $name, 'is_active' => 1])->first();
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
