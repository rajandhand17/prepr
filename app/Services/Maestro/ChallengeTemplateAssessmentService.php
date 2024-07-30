<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeAssessment;
use App\Models\ChallengeTemplateAssessment;
use Exception;

class ChallengeTemplateAssessmentService
{
    public static function addChallengeTemplateAssessment($challengeId, $templateChallengeId)
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
}
