<?php

namespace App\Services\Manage;

use App\Models\ChallengeSponsor;
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
}
