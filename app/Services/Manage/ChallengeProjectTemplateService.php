<?php

namespace App\Services\Manage;

use App\Models\ChallengeProjectTemplate;
use Exception;

class ChallengeProjectTemplateService
{
    public function createChallengeProjectTemplate($request, $challenge)
    {
        try {
            $challengeProjectTemplate = new ChallengeProjectTemplate();
            $challengeProjectTemplate->challenge_id = $challenge;
            $challengeProjectTemplate->template_id = $request->template_id;
            $challengeProjectTemplate->save();

            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    public function updateChallengeProjectTemplate($request, $challenge_id)
    {
        try {
            $challengeProjectTemplate = ChallengeProjectTemplate::where('challenge_id', $challenge_id)->first();
            $challengeProjectTemplate->template_id = ($request->has('template_id')) ? $request->template_id : $challengeProjectTemplate->template_id;
            $challengeProjectTemplate->save();

            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}
