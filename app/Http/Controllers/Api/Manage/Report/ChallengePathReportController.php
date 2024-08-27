<?php

namespace App\Http\Controllers\Api\Manage\Report;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Manage\ChallengePath\ChallengePathRepository;
use App\Repositories\Api\Manage\Report\Challenge\ChallengeReportRepository;
use Symfony\Component\HttpFoundation\Response;

class ChallengePathReportController extends AppBaseController
{
    public function __construct(
        protected ChallengeReportRepository $challengeReportRepository,
        protected ChallengePathRepository $challengePathRepository
    ) {
    }

    public function challengePathMemberProgress(string $slug)
    {
        try {
            $challenge = $this->challengePathRepository->getChallengePathBasedOnSlug($slug);

            if ($challenge) {
                $memberProgress = $this->challengeReportRepository->getChallengePathMemberProgress($challenge);

                if ($memberProgress !== false) {
                    return $this->sendResponse($memberProgress, __('Challenge Path member progress'));
                }

                return $this->sendError(__('responses.failed_to_fetch_challenge_path_member_progress'), Response::HTTP_BAD_REQUEST);
            }

            return $this->sendError(__('responses.not_found_challenge_path_view'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_challenge_path_member_progress'), Response::HTTP_BAD_REQUEST);
        }
    }
}
