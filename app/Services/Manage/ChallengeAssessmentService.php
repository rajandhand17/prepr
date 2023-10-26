<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Models\ChallengeAssessment;
use App\Services\UserService;
use Exception;

class ChallengeAssessmentService
{
    public static function uploadChallengeAssessment($attachment)
    {
        try {
            $upload_assessment_image = FileUploadHelper::uploadImageToS3($attachment, 'assessment');
            if ($upload_assessment_image == false) {
                return false;
            }

            return $upload_assessment_image;
        } catch (Exception $e) {
            return false;
        }
    }

    public function createChallengeAssessment($request, $challenge, $upload_assessment_attachment)
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

                if ($request->assessment_type == 'close' && $request->members_email !== null) {
                    foreach ($request->members_email as $key => $value) {
                        $challengeAssessment = new ChallengeAssessment();
                        $challengeAssessment->challenge_id = $challenge;
                        $challengeAssessment->assessment_type = $challenge_assessment_type;
                        $challengeAssessment->visibility = $challenge_visibility_type;
                        $challengeAssessment->members_email = $request->members_email[$key];
                        $challengeAssessment->guidelines = $request->guidelines;
                        $challengeAssessment->attachments = $upload_assessment_attachment;
                        $challengeAssessment->save();
                    }
                } else {
                    $challengeAssessment = new ChallengeAssessment();
                    $challengeAssessment->challenge_id = $challenge;
                    $challengeAssessment->assessment_type = $challenge_assessment_type;
                    $challengeAssessment->visibility = $challenge_visibility_type;
                    $challengeAssessment->members_email = null;
                    $challengeAssessment->guidelines = $request->guidelines;
                    $challengeAssessment->attachments = $upload_assessment_attachment;
                    $challengeAssessment->save();
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateChallengeAssessment($request, $challenge_id, $update_assessment_attachment)
    {
        try {
            $challengeAssessment = ChallengeAssessment::where('challenge_id', $challenge_id)->get();
            ChallengeAssessment::where('challenge_id', $challenge_id)->delete();
            if ($request->assessment_type !== null && $request->assessment_type !== "null") {
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

                if ($request->assessment_type == 'close' && $request->members_email !== null) {
                    foreach ($request->members_email as $key => $value) {
                        $challengeAssessment = new ChallengeAssessment();
                        $challengeAssessment->challenge_id = $challenge_id;
                        $challengeAssessment->assessment_type = $challenge_assessment_type;
                        $challengeAssessment->visibility = $challenge_visibility_type;
                        $challengeAssessment->members_email = $request->members_email[$key];
                        $challengeAssessment->guidelines = $request->guidelines;
                        $challengeAssessment->attachments = $update_assessment_attachment;
                        $challengeAssessment->save();
                    }
                } else {
                    $challengeAssessment = new ChallengeAssessment();
                    $challengeAssessment->challenge_id = $challenge_id;
                    $challengeAssessment->assessment_type = $challenge_assessment_type;
                    $challengeAssessment->visibility = $challenge_visibility_type;
                    $challengeAssessment->members_email = null;
                    $challengeAssessment->guidelines = $request->guidelines;
                    $challengeAssessment->attachments = $update_assessment_attachment;
                    $challengeAssessment->save();
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeAssessmentData($challengeAssessment)
    {
        try {
            $challenge_assessment = [];
            $assessmentTypeMapping = [
                '0' => 'none',
                '1' => 'open',
                '2' => 'close',
            ];

            $visibilityMapping = [
                '0' => 'null',
                '1' => 'users',
                '2' => 'hidden',
            ];

            $assessmentType = $assessmentTypeMapping[$challengeAssessment[0]->assessment_type] ?? 'none';
            $visibility = $visibilityMapping[$challengeAssessment[0]->visibility] ?? 'null';

            $members = [];
            if ($challengeAssessment->isNotEmpty()) {
                $memberEmails = $challengeAssessment->pluck('members_email');

                foreach ($memberEmails as $memberEmail) {
                    $getUser = UserService::getUserByEmail($memberEmail);
                    $getMemberDetail = [
                        'id'    => $getUser->id ?? null,
                        'email' => $getUser->email ?? $memberEmail,
                        'name'  => $getUser->full_name ?? null,
                    ];
                    $members[] = $getMemberDetail;
                }
            }

            $challenge_assessment = [
                'assessment_type'  => $assessmentType,
                'visibility'       => $visibility,
                'guidelines'       => $challengeAssessment[0]->guidelines,
                'attachments'      => $challengeAssessment[0]->attachments,
                'members'          => $members,
            ];

            return $challenge_assessment;
        } catch (Exception $e) {
            return false;
        }
    }
}
