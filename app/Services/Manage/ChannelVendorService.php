<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChannelVendor;

class ChannelVendorService
{
    public static function findVendorByApiKeyAndSecret($apiKey, $secret)
    {
        try {
            return ChannelVendor::where(['api_key' => $apiKey, 'secret_key' => $secret])->first();
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
