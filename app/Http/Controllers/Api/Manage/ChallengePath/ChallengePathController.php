<?php

namespace App\Http\Controllers\Api\Manage\ChallengePath;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\ChallengePath\CreateChallengePathRequest;
use App\Repositories\Api\Manage\ChallengePath\ChallengePathRepository;
use Exception;
use Illuminate\Http\Request;

class ChallengePathController extends AppBaseController
{
    private $challengePathRepository;

    public function __construct(ChallengePathRepository $challengePathRepository)
    {
        $this->challengePathRepository = $challengePathRepository;
    }

    public function create(CreateChallengePathRequest $request)
    {
        try {
            $upload_cover_image = config('site-settings.default_challenge_path_cover_image');
            if ($request->media !== null) {
                $uploaded_cover_image = $this->challengePathRepository->uploadChallengePathMedia($request->media);
                if (!$uploaded_cover_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_cover_image = $uploaded_cover_image;
            }

            $upload_achievement_image = config('site-settings.default_challenge_path_profile_image');
            if ($request->achievement_image !== null) {
                $uploaded_achievement_image = $this->challengePathRepository->uploadAchievementImage($request->achievement_image);
                if (!$uploaded_achievement_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_achievement_image = $uploaded_achievement_image;
            }

            $createChallengePath = $this->challengePathRepository->createChallengePath($upload_cover_image, $upload_achievement_image, $request);
            if ($createChallengePath) {
                return $this->sendResponse($createChallengePath, __('responses.challenge_path_stored_success'), 200);
            }

            return $this->sendError(__('responses.challenge_path_stored_failed'), 403);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkSlug($slug, Request $request)
    {
        try {
            $checkChallengePathSlugExistsOrNot = $this->challengePathRepository->checkSlug($slug);
            if ($checkChallengePathSlugExistsOrNot == false) {
                return $this->sendResponse([], __('responses.challenge_path_slug_available'), 200);
            }

            return $this->sendError(__('responses.challenge_path_already_exists'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
