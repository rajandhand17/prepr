<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeProjectTemplate;
use App\Services\ChallengePitchService;
use App\Services\ChallengeTaskService;
use App\Services\PitchTemplateService;
use Exception;
use Illuminate\Support\Facades\Log;

class ChallengeProjectTemplateService
{
    public function createChallengeProjectTemplate($request, $challenge_id, $createChallengeProjectPitch = null)
    {
        try {
            $challengeProjectTemplate = new ChallengeProjectTemplate();
            $challengeProjectTemplate->challenge_id = $challenge_id;
            if ($request->template_type == 'new') {
                $newTemplate = PitchTemplateService::addPitchAndTaskTemplate($request->title);
                if ($newTemplate) {
                    if ($request->has('pitch_questions') && count($request->pitch_questions) > 0) {
                        $storeChallengePitches = ChallengePitchService::storeChallengePitches($newTemplate->id, $request);
                    }

                    if ($request->has('task_questions') && count($request->task_questions) > 0) {
                        $storeChallengeTasks = ChallengeTaskService::storeChallengeTasks($newTemplate->id, $request);
                    }
                }
                $challengeProjectTemplate->template_id = $newTemplate->id;
            } else {
                if ($createChallengeProjectPitch) {
                    $challengeProjectTemplate->template_id = $createChallengeProjectPitch->id;
                } else {
                    $challengeProjectTemplate->template_id = $request->template_id;
                }
            }

            $challengeProjectTemplate->save();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in createChallengeProjectTemplate in ChallengeProjectTemplateService.php: '.$e->getMessage());

            return false;
        }
    }

    public function updateChallengeProjectTemplate($request, $challenge_id)
    {
        try {
            $challengeProjectTemplate = ChallengeProjectTemplate::where('challenge_id', $challenge_id)->first();
            if ($request->template_type == 'new') {
                $newTemplate = PitchTemplateService::addPitchAndTaskTemplate($request->title);
                if ($newTemplate) {
                    if ($request->has('pitch_questions') && count($request->pitch_questions) > 0) {
                        $storeChallengePitches = ChallengePitchService::storeChallengePitches($newTemplate->id, $request);
                    }

                    if ($request->has('task_questions') && count($request->task_questions) > 0) {
                        $storeChallengeTasks = ChallengeTaskService::storeChallengeTasks($newTemplate->id, $request);
                    }
                }
                $challengeProjectTemplate->template_id = $newTemplate->id;
            } else {
                $challengeProjectTemplate->template_id = ($request->has('template_id')) ? $request->template_id : $challengeProjectTemplate->template_id;
            }

            $challengeProjectTemplate->save();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

            return false;
        }
    }
}
