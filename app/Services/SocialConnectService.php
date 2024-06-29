<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\SocialConnect;

class SocialConnectService
{
    public function getSocialConnect($search = null)
    {
        try {
            $social_connect_list = SocialConnect::select('id', 'title', 'logo');

            //take 20 results based from the table
            $social_connect_list = $social_connect_list->where('integration_status', '1')->get();
            //check if there are any results
            if (!$social_connect_list->isEmpty()) {
                return $social_connect_list;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
