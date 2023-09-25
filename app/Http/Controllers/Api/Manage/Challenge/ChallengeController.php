<?php

namespace App\Http\Controllers\Api\Manage\Challenge;

use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Manage\Challenge\ChallengeRepository;
use App\Repositories\Api\Manage\ChallengeAchievement\ChallengeAchievementRepository;
use App\Repositories\Api\Manage\ChallengeSponsor\ChallengeSponsorRepository;
use Exception;
use Illuminate\Http\Request;

class ChallengeController extends AppBaseController
{
    private ChallengeRepository $challengeRepository;
    private ChallengeAchievementRepository $challengeAchievementRepository;
    private ChallengeSponsorRepository $challengeSponsorRepository;

    public function __construct(ChallengeRepository $challengeRepository, ChallengeAchievementRepository $challengeAchievementRepository, ChallengeSponsorRepository $challengeSponsorRepository)
    {
        $this->challengeRepository = $challengeRepository;
        $this->challengeAchievementRepository = $challengeAchievementRepository;
        $this->challengeSponsorRepository = $challengeSponsorRepository;
    }

    public function create(Request $request)
    {
        try {
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
                $uploaded_achievement_image = $this->challengeAchievementRepository->uploadChallengeAchievementImage($request->achievement_image);
                if (!$uploaded_achievement_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_achievement_image = $uploaded_achievement_image;
            }
            $createChallenge = $this->challengeRepository->createChallenge($request, $upload_cover_image, $upload_achievement_image);
            dd($createChallenge);
        } catch (Exception $th) {
            dd($th, 'In Controller');

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkSlug($slug)
    {
        try {
            $checkLabSlugExistsOrNot = $this->challengeRepository->checkSlug($slug);
            if ($checkLabSlugExistsOrNot == false) {
                return $this->sendResponse([], __('responses.challenge_slug_available'), 200);
            }

            return $this->sendError(__('responses.already_exists'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkName($title)
    {
        try {
            $checkLabNameExistsOrNot = $this->challengeRepository->checkNameExistsOrNot($title);
            if ($checkLabNameExistsOrNot) {
                return $this->sendError(__('responses.challenge_name_not_available'));
            }

            return $this->sendResponse([], __('responses.challenge_name_available'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
