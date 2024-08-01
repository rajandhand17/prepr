<?php

namespace App\Services\Maestro;

use App\Helpers\FileUploadHelper;
use App\Models\ChallengeAssessment;
use Exception;

class ChallengeAssessmentService
{
    public static function getAssessment($challengeId)
    {
        try {
            $assessment = ChallengeAssessment::where('challenge_id', $challengeId)->first();
            if ($assessment) {
                return $assessment;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function storeUpdateAssessment($request)
    {
        try {
            switch ($request->assessment_type) {
                case '0':
                    $attachmentName = config('constants.assessment_type.no_evaluation');
                    $guidelines = $request->noEvGuidelines;
                    $memberEmails = null;
                    break;
                case '1':
                    $attachmentName = config('constants.assessment_type.open_evaluation');
                    $guidelines = $request->openEvGuidelines;
                    $memberEmails = null;
                    $guidelines = $request->openEvGuidelines;
                    break;
                case '2':
                    $attachmentName = config('constants.assessment_type.close_evaluation');
                    $guidelines = $request->closeEvGuidelines;
                    $memberEmails = json_encode($request->members_email);
                    break;
                default:
                    $attachmentName = config('constants.assessment_type.no_evaluation');
                    $guidelines = $request->noEvGuidelines;
                    $memberEmails = null;
            }
            $visibility = (isset($request->visibility) && $request->visibility == 'on') ? '1' : '0';

            if ($request->file($attachmentName)) {
                $attachment = FileUploadHelper::uploadImageToS3($request->file($attachmentName), 'challenge_assessment');
            } else {
                $attachment = null;
            }

            if ($request->request_type == 'create') {
                ChallengeAssessment::create(['challenge_id' => $request->challenge_id, 'assessment_type' => $request->assessment_type, 'visibility' => $visibility, 'members_email' => $memberEmails, 'guidelines' => $guidelines, 'attachments' => $attachment]);
            } elseif ($request->request_type == 'update') {
                ChallengeAssessment::where('id', $request->assessment_id)->update(['challenge_id' => $request->challenge_id, 'assessment_type' => $request->assessment_type, 'visibility' => $visibility, 'members_email' => $memberEmails, 'guidelines' => $guidelines, 'attachments' => $attachment]);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
