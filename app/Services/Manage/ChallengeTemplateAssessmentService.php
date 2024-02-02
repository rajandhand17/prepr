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
                $challengeTemplateAssessment->visibility = ($challengeAssessment->assessment_type == '2') ? '1' : $challengeAssessment->visibility;
                $challengeTemplateAssessment->members_email = ($challengeAssessment->assessment_type == '2') ? null : $challengeAssessment->members_email;
                $challengeTemplateAssessment->guidelines = $challengeAssessment->guidelines;
                $challengeTemplateAssessment->attachments = $challengeAssessment->attachments;
                $challengeTemplateAssessment->save();
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function redeemChallengeTemplateAssessment($redeemChallengeId, $challengeTemplateId)
    {
        try {
            $checkChallengeTemplateAssessments = ChallengeTemplateAssessment::where('challenge_template_id', $challengeTemplateId)->get();
            if (!empty($checkChallengeTemplateAssessments)) {
                foreach ($checkChallengeTemplateAssessments as $challengeTemplateAssessment) {
                    $newChallengeAssessments = new ChallengeAssessment();
                    $newChallengeAssessments->challenge_id = $redeemChallengeId;
                    $newChallengeAssessments->assessment_type = $challengeTemplateAssessment->assessment_type;
                    $newChallengeAssessments->visibility = ($challengeTemplateAssessment->assessment_type == '2') ? '1' : $challengeTemplateAssessment->visibility;
                    $newChallengeAssessments->members_email = ($challengeTemplateAssessment->assessment_type == '2') ? null : $challengeTemplateAssessment->members_email;
                    $newChallengeAssessments->guidelines = $challengeTemplateAssessment->guidelines;
                    $newChallengeAssessments->attachments = $challengeTemplateAssessment->attachments;
                    $newChallengeAssessments->save();
                }
            }

            return true;
        } catch (Exception $e) {
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
            return false;
        }
    }
}
