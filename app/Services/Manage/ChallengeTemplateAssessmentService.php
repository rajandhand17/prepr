<?php

namespace App\Services\Manage;

use App\Models\ChallengeAssessment;
use App\Models\ChallengeTemplateAssessment;
use Exception;

class ChallengeTemplateAssessmentService
{
    public function addChallengeTemplateAssessment($challengeId, $templateChallengeId)
    {
        try {
            $challengeAssessments = ChallengeAssessment::where('challenge_id', $challengeId)->get();
            foreach ($challengeAssessments as $challengeAssessment) {
                $challengeTemplateAssessment = new ChallengeTemplateAssessment();
                $challengeTemplateAssessment->challenge_template_id = $templateChallengeId;
                $challengeTemplateAssessment->assessment_type = $challengeAssessment->assessment_type;
                $challengeTemplateAssessment->visibility = $challengeAssessment->visibility;
                $challengeTemplateAssessment->members_email = $challengeAssessment->members_email;
                $challengeTemplateAssessment->guidelines = $challengeAssessment->guidelines;
                $challengeTemplateAssessment->attachments = $challengeAssessment->attachments;
                $challengeTemplateAssessment->save();
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
