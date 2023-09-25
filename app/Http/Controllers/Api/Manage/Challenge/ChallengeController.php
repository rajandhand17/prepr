<?php

namespace App\Http\Controllers\Api\Manage\Challenge;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\Challenge\CreateChallengeRequest;
use App\Http\Resources\Manage\Challenge\ChallengeResource;
use App\Repositories\Api\Manage\Challenge\ChallengeRepository;
use Illuminate\Http\Request;
use Exception;

class ChallengeController extends AppBaseController
{
    private ChallengeRepository $challengeRepository;

    public function __construct(ChallengeRepository $challengeRepository)
    {
        $this->challengeRepository = $challengeRepository;
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
            $createChallenge = $this->challengeRepository->createChallenge($request, $upload_cover_image, $upload_achievement_image);

            if ($createChallenge != false) {
                return $this->sendResponse(ChallengeResource::make($createChallenge), __('responses.challenge_stored_success'), 200);
            }

            return $this->sendError(__('responses.challenge_stored_failed'), 400);
        } catch (Exception $th) {
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
        } catch (\Exception $e) {
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
        } catch (Exception $th) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
