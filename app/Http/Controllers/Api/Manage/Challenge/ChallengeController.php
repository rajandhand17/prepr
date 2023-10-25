<?php

namespace App\Http\Controllers\Api\Manage\Challenge;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\Challenge\CreateChallengeRequest;
use App\Http\Requests\Manage\Challenge\UpdateChallengeRequest;
use App\Http\Resources\Manage\Challenge\ChallengeAssessmentResource;
use App\Http\Resources\Manage\Challenge\ChallengeResource;
use App\Repositories\Api\Manage\Challenge\ChallengeRepository;
use App\Services\Manage\OrganizationService;
use Exception;
use Illuminate\Http\Request;

class ChallengeController extends AppBaseController
{
    private ChallengeRepository $challengeRepository;

    public function __construct(ChallengeRepository $challengeRepository)
    {
        $this->challengeRepository = $challengeRepository;
    }

    public function index(Request $request)
    {
        try {
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_found'), 404);
            }

            $challenge = $this->challengeRepository->getChallengeList($request, $organization);
            if ($challenge) {
                $response = [
                    'total_count'  => $challenge->total(),
                    'per_page'     => $challenge->perPage(),
                    'count'        => $challenge->count(),
                    'current_page' => $challenge->currentPage(),
                    'total_pages'  => $challenge->lastPage(),
                    'list'         => ChallengeResource::collection($challenge),
                ];

                return $this->sendResponse($response, __('responses.found_challenges_list'));
            }

            return $this->sendError(__('responses.not_found_challenges_list'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create(CreateChallengeRequest $request)
    {
        try {
            // if (!auth()->user()->isAbleTo('create_challenge')) {
            //     return $this->sendError(__('responses.permission_forbidden'), 403);
            // }
            $upload_cover_image = config('site-settings.default_challenge_cover_image');
            if ($request->cover_image !== null) {
                $uploaded_cover_image = $this->challengeRepository->uploadChallengeCoverImage($request->cover_image);
                if (!$uploaded_cover_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_cover_image = $uploaded_cover_image;
            }

            $upload_achievement_image = config('site-settings.default_challenge_achievement_image');
            if ($request->achievement_image !== null) {
                $uploaded_achievement_image = $this->challengeRepository->uploadChallengeParticipationAchievementImage($request->achievement_image);
                if (!$uploaded_achievement_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_achievement_image = $uploaded_achievement_image;
            }

            $upload_assessment_attachment = config('site-settings.default_challenge_cover_image');
            if ($request->attachments !== null) {
                $uploaded_assessment_attachment = $this->challengeRepository->uploadChallengeAssessment($request->attachments);
                if (!$uploaded_assessment_attachment) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_assessment_attachment = $uploaded_assessment_attachment;
            }

            $createChallenge = $this->challengeRepository->createChallenge($request, $upload_cover_image, $upload_achievement_image, $upload_assessment_attachment);

            if ($createChallenge != false) {
                return $this->sendResponse(ChallengeResource::make($createChallenge), __('responses.challenge_stored_success'), 200);
            }

            return $this->sendError(__('responses.challenge_stored_failed'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            // if (!auth()->user()->isAbleTo('view_challenge', $challenge)) {
            //     return $this->sendError(__('responses.permission_forbidden'), 403);
            // }
            if ($challenge) {
                return $this->sendResponse(ChallengeResource::make($challenge), __('responses.found_challenge_detail'), 200);
            }

            return $this->sendError(__('responses.found_not_challenge_detail'), 404);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function update($slug, UpdateChallengeRequest $request)
    {
        try {
            $checkComponentBasedOnSlug = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }
            $update_cover_image = str_replace(config('site-settings.aws_url'), '', $checkComponentBasedOnSlug->media);
            $update_participation_achievement_image = str_replace(config('site-settings.aws_url'), '', $checkComponentBasedOnSlug->participation_achievement->achievement_image);
            $update_assessment_attachment = str_replace(config('site-settings.aws_url'), '', $checkComponentBasedOnSlug->challenge_assessment[0]->attachments);
            if ($request->cover_image !== null) {
                $uploaded_cover_image = $this->challengeRepository->uploadChallengeCoverImage($request->cover_image);
                if ($uploaded_cover_image == false) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $update_cover_image = $uploaded_cover_image;
            }

            if ($request->achievement_image !== null) {
                $updated_challenge_achievement_image = $this->challengeRepository->uploadChallengeParticipationAchievementImage($request->achievement_image);
                if ($updated_challenge_achievement_image == false) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $update_participation_achievement_image = $updated_challenge_achievement_image;
            }

            if ($request->attachments !== null) {
                $updated_assessment_attachment = $this->challengeRepository->uploadChallengeAssessment($request->attachments);
                if ($updated_assessment_attachment == false) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $update_assessment_attachment = $updated_assessment_attachment;
            }

            $updateChallenge = $this->challengeRepository->updateChallenge($slug, $request, $update_cover_image, $update_participation_achievement_image, $update_assessment_attachment);
            if ($updateChallenge != false) {
                return $this->sendResponse(ChallengeResource::make($updateChallenge), __('responses.challenge_update_successfully'), 200);
            }

            return $this->sendError(__('responses.challenge_not_update'));
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function delete($slug, Request $request)
    {
        try {
            $checkComponentBasedOnSlug = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }
            // if (!auth()->user()->isAbleTo('delete_challenge', $checkComponentBasedOnSlug)) {
            //     return $this->sendError(__('responses.challenge_delete_access_denied'), 403);
            // }
            $challenge = $this->challengeRepository->deleteChallenge($checkComponentBasedOnSlug->id, $request);
            if ($challenge) {
                return $this->sendResponse(null, __('responses.challenge_delete'));
            }

            return $this->sendError(__('responses.challenge_not_delete'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkSlug($slug)
    {
        try {
            $checkChallengeSlugExistsOrNot = $this->challengeRepository->checkSlug($slug);
            if ($checkChallengeSlugExistsOrNot == false) {
                return $this->sendResponse([], __('responses.challenge_slug_available'), 200);
            }

            return $this->sendError(__('responses.already_exists'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkName($title)
    {
        try {
            $checkChallengeNameExistsOrNot = $this->challengeRepository->checkNameExistsOrNot($title);
            if ($checkChallengeNameExistsOrNot) {
                return $this->sendError(__('responses.challenge_name_not_available'));
            }

            return $this->sendResponse([], __('responses.challenge_name_available'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fetchAssessment($slug)
    {
        try {
            $checkChallengeSlugExistsOrNot = $this->challengeRepository->checkSlug($slug);
            if ($checkChallengeSlugExistsOrNot == false) {
                return $this->sendResponse([], __('responses.challenge_slug_available'), 200);
            }
            $getChallengeAssessment = [];
            if ($checkChallengeSlugExistsOrNot->challenge_assessment->isNotEmpty()) {
                $getChallengeAssessment = $this->challengeRepository->getChallengeAssessmentData($checkChallengeSlugExistsOrNot->challenge_assessment);
            }
            if (!empty($getChallengeAssessment)) {
                return $this->sendResponse(ChallengeAssessmentResource::make($getChallengeAssessment), __('responses.found_challenge_assessment_detail'), 200);
            }

            return $this->sendResponse([], __('responses.found_not_challenge_assessment_detail'));
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
