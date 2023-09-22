<?php

namespace App\Http\Controllers\Api\Manage\Challenge;

use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Manage\Challenge\ChallengeRepository;
use Exception;
use Illuminate\Http\Request;

class ChallengeController extends AppBaseController
{
    private ChallengeRepository $challengeRepository;

    public function __construct(ChallengeRepository $challengeRepository)
    {
        $this->challengeRepository = $challengeRepository;
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

            $upload_achievement_image = config('site-settings.default_challenge_profile_image');

            $challenge = $this->challengeRepository->createChallenge($request, $upload_cover_image);
        } catch (Exception $th) {
            dd($th, 'In Controller');

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
