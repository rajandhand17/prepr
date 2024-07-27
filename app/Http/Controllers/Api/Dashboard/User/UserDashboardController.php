<?php

namespace App\Http\Controllers\Api\Dashboard\User;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\Challenge\ChallengeResource;
use App\Repositories\Api\Dashboard\User\UserDashboardRepository;
use Exception;
use Illuminate\Http\Request;

class UserDashboardController extends AppBaseController
{
    private $userDashboardRepository;

    public function __construct(UserDashboardRepository $userDashboardRepository)
    {
        $this->userDashboardRepository = $userDashboardRepository;
    }

    public function getMyChallenges(Request $request)
    {
        try {
            // Check valid request or not for my challenge request
            if (!in_array($request->type, ['my', 'invites', 'favourite'])) {
                return $this->sendError(__('responses.handler_bad_request'), 402);
            }

            // Fetch challenge ids based on request
            $userData = auth()->user();
            switch ($request->type) {
                case 'my':
                    $inviteStatus = config('constants.member_management_invite_status.accepted');
                    $challengeIds = $this->userDashboardRepository->challengeRequestIds($userData, $inviteStatus);
                    break;
                case 'invites':
                    $inviteStatus = config('constants.member_management_invite_status.invited');
                    $challengeIds = $this->userDashboardRepository->challengeRequestIds($userData, $inviteStatus);
                    break;
                case 'favourite':
                    $challengeIds = $this->userDashboardRepository->challengeFavouriteIds($userData);
                    break;
            }

            $challenges = $this->userDashboardRepository->getChallengeList($challengeIds);
            if ($challenges !== false) {
                $response = [
                    'total_count'  => $challenges->total(),
                    'per_page'     => $challenges->perPage(),
                    'count'        => $challenges->count(),
                    'current_page' => $challenges->currentPage(),
                    'total_pages'  => $challenges->lastPage(),
                    'list'         => ChallengeResource::collection($challenges),
                ];

                return $this->sendResponse($response, __('responses.found_challenges_list'));
            }

            return $this->sendError(__('responses.not_found_challenges_list'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
