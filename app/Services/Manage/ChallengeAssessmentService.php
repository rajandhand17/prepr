<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Models\ChallengeAssessment;
use Exception;

class ChallengeAssessmentService
{
    public function createChallengeAssessment($request, $challenge)
    {
        try {
            if ($request->assessment_type !== null) {
                $challenge_assessment_type = config('constants.challenge_assessment_type.null');
                switch ($request->assessment_type) {
                    case 'close':
                        $challenge_assessment_type = config('constants.challenge_assessment_type.close');
                        break;
                    case 'open':
                        $challenge_assessment_type = config('constants.challenge_assessment_type.open');
                        break;
                    default:
                        $challenge_assessment_type = config('constants.challenge_assessment_type.null');
                        break;
                }

                $challenge_visibility_type = config('constants.challenge_visibility_type.users');
                switch ($request->visibility == 'close') {
                    case 'hidden':
                        $challenge_visibility_type = config('constants.challenge_visibility_type.hidden');
                        break;
                    case 'users':
                        $challenge_visibility_type = config('constants.challenge_visibility_type.users');
                        break;
                    default:
                        $challenge_visibility_type = config('constants.challenge_visibility_type.users');
                        break;
                }

                $upload_assessment_image = FileUploadHelper::uploadImageToS3($request->attachments, 'assessment');
                if ($request->assessment_type == 'close' && $request->members_email !== null) {
                    foreach ($request->members_email as $key => $value) {
                        $challengeAssessment = new ChallengeAssessment();
                        $challengeAssessment->challenge_id = $challenge;
                        $challengeAssessment->assessment_type = $challenge_assessment_type;
                        $challengeAssessment->visibility = $challenge_visibility_type;
                        $challengeAssessment->members_email = $request->members_email[$key]; // TODO confirm with Vinod about email
                        $challengeAssessment->guidelines = $request->guidelines;
                        $challengeAssessment->attachments = $upload_assessment_image;
                        $challengeAssessment->save();
                    }
                } else {
                    $challengeAssessment = new ChallengeAssessment();
                    $challengeAssessment->challenge_id = $challenge;
                    $challengeAssessment->assessment_type = $challenge_assessment_type;
                    $challengeAssessment->visibility = $challenge_visibility_type;
                    $challengeAssessment->members_email = null;
                    $challengeAssessment->guidelines = $request->guidelines;
                    $challengeAssessment->attachments = $upload_assessment_image;
                    $challengeAssessment->save();
                }
            }

            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}
