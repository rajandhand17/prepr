<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeAssessment;
use App\Models\ChallengeTemplateAssessment;
use Exception;

class ChallengeTemplateAssessmentService
{
    public function addChallengeTemplateAssessment($challengeId, $templateChallengeId)
    {
        try {
            $challengeTemplateAssessment = true;
            $challengeAssessments = ChallengeAssessment::where('challenge_id', $challengeId)->first();
            if ($challengeAssessments) {
                $challengeTemplateAssessment = new ChallengeTemplateAssessment();
                $challengeTemplateAssessment->challenge_template_id = $templateChallengeId;
                $challengeTemplateAssessment->assessment_type = $challengeAssessments->assessment_type;
                $challengeTemplateAssessment->visibility = ($challengeAssessments->assessment_type == '2') ? '1' : $challengeAssessments->visibility;
                $challengeTemplateAssessment->members_email = ($challengeAssessments->assessment_type == '2') ? null : $challengeAssessments->members_email;
                $challengeTemplateAssessment->guidelines = $challengeAssessments->guidelines;
                $challengeTemplateAssessment->attachments = $challengeAssessments->attachments;
                $challengeTemplateAssessment->save();
            }

            return $challengeTemplateAssessment;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function redeemChallengeTemplateAssessment($redeemChallengeId, $challengeTemplateId)
    {
        try {
            $newChallengeAssessments = true;
            $checkChallengeTemplateAssessments = ChallengeTemplateAssessment::where('challenge_template_id', $challengeTemplateId)->first();
            if ($checkChallengeTemplateAssessments) {
                $newChallengeAssessments = new ChallengeAssessment();
                $newChallengeAssessments->challenge_id = $redeemChallengeId;
                $newChallengeAssessments->assessment_type = $checkChallengeTemplateAssessments->assessment_type;
                $newChallengeAssessments->visibility = ($checkChallengeTemplateAssessments->assessment_type == '2') ? '1' : $checkChallengeTemplateAssessments->visibility;
                $newChallengeAssessments->members_email = ($checkChallengeTemplateAssessments->assessment_type == '2') ? null : $checkChallengeTemplateAssessments->members_email;
                $newChallengeAssessments->guidelines = $checkChallengeTemplateAssessments->guidelines;
                $newChallengeAssessments->attachments = $checkChallengeTemplateAssessments->attachments;
                $newChallengeAssessments->save();
            }

            return $newChallengeAssessments;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteChallengeTemplateAssessment($challengeTemplateId)
    {
        try {
            $challengeTemplateAssessment = ChallengeTemplateAssessment::where('challenge_template_id', $challengeTemplateId)->get();
            if ($challengeTemplateAssessment->isNotEmpty()) {
                $deleteChallengeTemplateAssessment = ChallengeTemplateAssessment::where('challenge_template_id', $challengeTemplateId)->delete();
                if (!$deleteChallengeTemplateAssessment) {
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
