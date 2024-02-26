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
            $challengeAssessment = true;
            if ($request->assessment_type !== null) {
                $challenge_assessment_type = config('constants.challenge_assessment_type.null');
                switch ($request->assessment_type) {
                    case 'closed':
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
                if ($request->assessment_type == 'closed') {
                    switch ($request->visibility) {
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
                }

                if ($request->assessment_type == 'closed' && $request->members_email !== null) {
                    $challengeAssessment = new ChallengeAssessment();
                    $challengeAssessment->challenge_id = $challenge;
                    $challengeAssessment->assessment_type = $challenge_assessment_type;
                    $challengeAssessment->visibility = $challenge_visibility_type;
                    $challengeAssessment->members_email = $request->members_email;
                    $challengeAssessment->guidelines = $request->guidelines;
                    $challengeAssessment->attachments = $upload_assessment_attachment;
                    $challengeAssessment->save();
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

            return $challengeAssessment;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateChallengeAssessment($request, $challenge_id, $update_assessment_attachment)
    {
        try {
            $updateChallengeAssessment = true;
            $challengeAssessment = ChallengeAssessment::where('challenge_id', $challenge_id)->get();
            ChallengeAssessment::where('challenge_id', $challenge_id)->delete();
            if ($request->assessment_type !== null && $request->assessment_type !== 'null') {
                $challenge_assessment_type = config('constants.challenge_assessment_type.null');
                switch ($request->assessment_type) {
                    case 'closed':
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
                if ($request->assessment_type == 'closed') {
                    switch ($request->visibility) {
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
                }

                if ($request->assessment_type == 'closed' && $request->members_email !== null) {
                    $updateChallengeAssessment = new ChallengeAssessment();
                    $updateChallengeAssessment->challenge_id = $challenge_id;
                    $updateChallengeAssessment->assessment_type = $challenge_assessment_type;
                    $updateChallengeAssessment->visibility = $challenge_visibility_type;
                    $updateChallengeAssessment->members_email = $request->members_email;
                    $updateChallengeAssessment->guidelines = $request->guidelines;
                    $updateChallengeAssessment->attachments = $update_assessment_attachment;
                    $updateChallengeAssessment->save();
                } else {
                    $updateChallengeAssessment = new ChallengeAssessment();
                    $updateChallengeAssessment->challenge_id = $challenge_id;
                    $updateChallengeAssessment->assessment_type = $challenge_assessment_type;
                    $updateChallengeAssessment->visibility = $challenge_visibility_type;
                    $updateChallengeAssessment->members_email = null;
                    $updateChallengeAssessment->guidelines = $request->guidelines;
                    $updateChallengeAssessment->attachments = $update_assessment_attachment;
                    $updateChallengeAssessment->save();
                }
            }

            return $updateChallengeAssessment;
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
                '2' => 'closed',
            ];

            $visibilityMapping = [
                '0' => 'null',
                '1' => 'users',
                '2' => 'hidden',
            ];

            $assessmentType = $assessmentTypeMapping[$challengeAssessment->assessment_type] ?? 'none';
            $visibility = $visibilityMapping[$challengeAssessment->visibility] ?? 'null';

            $members = [];
            if ($challengeAssessment) {
                $memberEmails = $challengeAssessment->members_email;

                if ($memberEmails != null) {
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
            }

            $challenge_assessment = [
                'assessment_type'  => $assessmentType,
                'visibility'       => $visibility,
                'guidelines'       => $challengeAssessment->guidelines,
                'attachments'      => $challengeAssessment->attachments,
                'members'          => $members,
            ];

            return $challenge_assessment;
        } catch (Exception $e) {
            return false;
        }
    }

    public function cloneChallengeAssessment($originalChallengeAssessment, $clonedChallengeId)
    {
        try {
            if ($originalChallengeAssessment) {
                $originalChallengeAssessment->each(function ($challenge_assessment) use ($clonedChallengeId) {
                    if ($challenge_assessment) {
                        $cloneAssessment = $challenge_assessment->replicate();
                        $cloneAssessment->challenge_id = $clonedChallengeId;
                        $cloneAssessment->save();
                    }
                });
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
