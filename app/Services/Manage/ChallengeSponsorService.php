<?php

namespace App\Services\Manage;

use App\Helpers\LanguageColumnHelper;
use App\Models\ChallengeSponsor;
use App\Models\Host;
use Exception;

class ChallengeSponsorService
{
    public function createChallengeSponsor($request, $challenge)
    {
        try {
            if ($request->host_id !== null) {
                foreach ($request->host_id as $key => $value) {
                    $challengeSponsor = new ChallengeSponsor();
                    $challengeSponsor->challenge_id = $challenge;
                    $challengeSponsor->host_id = $request->host_id[$key];
                    $challengeSponsor->save();
                }
            }

            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    public static function getHostBasedOnIds($host_ids)
    {
        try {
            $getHostsList = Host::select('id', LanguageColumnHelper::getLanguageColumnName(app()->getLocale(), 'title').' as title')
            ->whereIn('id', $host_ids)->get();
            if ($getHostsList) {
                return $getHostsList;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
