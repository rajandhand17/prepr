<?php

namespace App\Services\Manage;

use App\Models\ChallengeProjectTemplate;
use Exception;
use Illuminate\Support\Facades\Log;

class ChallengeProjectTemplateService
{
    public function createChallengeProjectTemplate($request, $challenge_id)
    {
        try {
            $challengeProjectTemplate = new ChallengeProjectTemplate();
            $challengeProjectTemplate->challenge_id = $challenge_id;
            $challengeProjectTemplate->template_id = $request->template_id;
            $challengeProjectTemplate->save();

            return true;
        } catch (Exception $e) {
            Log::error('Error in createChallengeProjectTemplate in ChallengeProjectTemplateService.php: '.$e->getMessage());

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
        } catch (Exception $e) {
            return false;
        }
    }

    public function cloneChallengeProjectTemplate($originalChallengeProjectTemplate, $clonedChallengeId)
    {
        try {
            if ($originalChallengeProjectTemplate) {
                $cloneIncentiveAchievement = $originalChallengeProjectTemplate->replicate();
                $cloneIncentiveAchievement->challenge_id = $clonedChallengeId;
                $cloneIncentiveAchievement->save();
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
