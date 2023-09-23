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
}
