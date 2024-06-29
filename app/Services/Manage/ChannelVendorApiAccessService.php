<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChannelVendorApiAccess;

class ChannelVendorApiAccessService
{
    public static function hasApiAccess($vendorId, $apiId)
    {
        try {
            return ChannelVendorApiAccess::where(['channel_vendor_id' => $vendorId, 'channel_api_id' => $apiId])->first();
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }
}
