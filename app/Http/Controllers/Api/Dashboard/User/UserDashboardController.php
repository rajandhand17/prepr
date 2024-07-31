<?php

namespace App\Http\Controllers\Api\Dashboard\User;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Dashboard\UpComingDeadlineResource;
use App\Http\Resources\Project\ProjectResource;
use App\Http\Resources\Public\Achievement\AchievementResource;
use App\Http\Resources\Public\Challenge\ChallengeResource;
use App\Http\Resources\Public\Lab\LabResource;
use App\Http\Resources\Public\ResourceModule\ResourceModuleResource;
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

    public function getMyLabs(Request $request)
    {
        try {
            // Check valid request or not for my lab request
            if (!in_array($request->type, ['my', 'invites', 'favourite'])) {
                return $this->sendError(__('responses.handler_bad_request'), 402);
            }

            // Fetch lab ids based on request
            $userData = auth()->user();
            switch ($request->type) {
                case 'my':
                    $inviteStatus = config('constants.member_management_invite_status.accepted');
                    $labIds = $this->userDashboardRepository->labRequestIds($userData, $inviteStatus);
                    break;
                case 'invites':
                    $inviteStatus = config('constants.member_management_invite_status.invited');
                    $labIds = $this->userDashboardRepository->labRequestIds($userData, $inviteStatus);
                    break;
                case 'favourite':
                    $labIds = $this->userDashboardRepository->labFavouriteIds($userData);
                    break;
            }

            $labs = $this->userDashboardRepository->getLabList($labIds);
            if ($labs !== false) {
                $response = [
                    'total_count'  => $labs->total(),
                    'per_page'     => $labs->perPage(),
                    'count'        => $labs->count(),
                    'current_page' => $labs->currentPage(),
                    'total_pages'  => $labs->lastPage(),
                    'list'         => LabResource::collection($labs),
                ];

                return $this->sendResponse($response, __('responses.found_labs_list'));
            }

            return $this->sendError(__('responses.not_found_labs_list'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getMyProjects(Request $request)
    {
        try {
            // Check valid request or not for my project request
            if (!in_array($request->type, ['my', 'invites', 'favourite'])) {
                return $this->sendError(__('responses.handler_bad_request'), 402);
            }

            // Fetch project ids based on request
            $userData = auth()->user();
            switch ($request->type) {
                case 'my':
                    $inviteStatus = config('constants.project_member_management_invite_status.accepted');
                    $projectIds = $this->userDashboardRepository->myProjectDashboardRequestIds($userData, $inviteStatus);
                    break;
                case 'invites':
                    $inviteStatus = config('constants.project_member_management_invite_status.invited');
                    $projectIds = $this->userDashboardRepository->invitesProjectDashboardRequestIds($userData, $inviteStatus);
                    break;
                case 'favourite':
                    $projectIds = $this->userDashboardRepository->projectFavouriteIds($userData);
                    break;
            }

            $projects = $this->userDashboardRepository->getDashboardProjectList($projectIds);
            if ($projects !== false) {
                $response = [
                    'total_count'  => $projects->total(),
                    'per_page'     => $projects->perPage(),
                    'count'        => $projects->count(),
                    'current_page' => $projects->currentPage(),
                    'total_pages'  => $projects->lastPage(),
                    'list'         => ProjectResource::collection($projects),
                ];

                return $this->sendResponse($response, __('responses.found_projects_list'));
            }

            return $this->sendError(__('responses.not_found_projects_list'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getMyResourceModules(Request $request)
    {
        try {
            // Check valid request or not for my resource module request
            if (!in_array($request->type, ['my', 'favourite'])) {
                return $this->sendError(__('responses.handler_bad_request'), 402);
            }

            // Fetch resource module ids based on request
            $userData = auth()->user();
            switch ($request->type) {
                case 'my':
                    $resourceModuleIds = $this->userDashboardRepository->myResourceModuleIds($userData);
                    break;
                case 'favourite':
                    $resourceModuleIds = $this->userDashboardRepository->resourceModuleFavouriteIds($userData);
                    break;
            }

            $resourceModules = $this->userDashboardRepository->getResourceModuleDashboardList($resourceModuleIds);
            if ($resourceModules !== false) {
                $response = [
                    'total_count'  => $resourceModules->total(),
                    'per_page'     => $resourceModules->perPage(),
                    'count'        => $resourceModules->count(),
                    'current_page' => $resourceModules->currentPage(),
                    'total_pages'  => $resourceModules->lastPage(),
                    'list'         => ResourceModuleResource::collection($resourceModules),
                ];

                return $this->sendResponse($response, __('responses.found_resource_module_list'));
            }

            return $this->sendError(__('responses.not_found_resource_module_list'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getMyLatestAchievement()
    {
        try {
            $userData = auth()->user();
            $getMyLatestAchievement = $this->userDashboardRepository->getMyLatestAchievement($userData);
            if ($getMyLatestAchievement != false) {
                return $this->sendResponse(AchievementResource::make($getMyLatestAchievement), __('responses.found_latest_achievement'));
            }

            return $this->sendError(__('responses.not_found_latest_achievement'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getMyRecommendations(Request $request)
    {
        try {
            // Check valid request or not for my recommendations request
            if (!in_array($request->type, ['challenges', 'labs', 'resource_modules'])) {
                return $this->sendError(__('responses.handler_bad_request'), 402);
            }

            $userData = auth()->user();

            // Fetch User Skills.
            $fetchUserSkills = $this->userDashboardRepository->fetchUserSkills($userData);
            if ($fetchUserSkills == false) {
                return $this->sendError(__('responses.user_skills_not_found'));
            }

            switch ($request->type) {
                case 'challenges':
                    $fetchRecommendedChallenges = $this->userDashboardRepository->fetchRecommendedChallenges($fetchUserSkills, $userData);

                    return $this->sendResponse(ChallengeResource::collection($fetchRecommendedChallenges), __('responses.challenge_recommended_found'), 200);
                    break;
                case 'labs':
                    $fetchRecommendedLabs = $this->userDashboardRepository->fetchRecommendedLabs($fetchUserSkills, $userData);

                    return $this->sendResponse(LabResource::collection($fetchRecommendedLabs), __('responses.lab_recommended_found'), 200);
                    break;
                case 'resource_modules':
                    $fetchRecommendedResourceModules = $this->userDashboardRepository->fetchRecommendedResourceModules($fetchUserSkills, $userData);

                    return $this->sendResponse(ResourceModuleResource::collection($fetchRecommendedResourceModules), __('responses.lab_recommended_found'), 200);
                    break;
            }

            return $this->sendError(__('responses.failed_to_find_recommended_data'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getMyProgress(Request $request)
    {
        try {
            // Check valid request or not for my recommendations request
            if (!in_array($request->type, ['challenges', 'labs', 'resource_modules'])) {
                return $this->sendError(__('responses.handler_bad_request'), 402);
            }

            // Fetch user's progress based on components
            $userData = auth()->user();
            switch ($request->type) {
                case 'challenges':
                    $fetchUserProgress = $this->userDashboardRepository->fetchMyChallengeProgress($userData);
                    $message = __('responses.user_challenges_progress_retrived');
                    break;
                case 'labs':
                    $fetchUserProgress = $this->userDashboardRepository->fetchMyLabProgress($userData);
                    $message = __('responses.user_labs_progress_retrived');
                    break;
                case 'resource_modules':
                    $fetchUserProgress = $this->userDashboardRepository->fetchMyResourceModuleProgress($userData);
                    $message = __('responses.user_resource_modules_progress_retrived');
                    break;
            }

            if (!empty($fetchUserProgress)) {
                return $this->sendResponse($fetchUserProgress, $message, 200);
            }

            return $this->sendError(__('responses.failed_to_retrieved_user_progress'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getUpComingChallengeDeadlines()
    {
        try {
            $userData = auth()->user();
            $inviteStatus = config('constants.member_management_invite_status.accepted');
            $challengeIds = $this->userDashboardRepository->challengeRequestIds($userData, $inviteStatus);

            $fetchUpComingDeadlineChallenges = $this->userDashboardRepository->fetchUpComingDeadlineChallenges($challengeIds, $userData);
            if (!empty($fetchUpComingDeadlineChallenges)) {
                return $this->sendResponse(UpComingDeadlineResource::collection($fetchUpComingDeadlineChallenges), __('responses.user_upcomming_deadline_retrieved'), 200);
            }

            return $this->sendError(__('responses.not_user_upcomming_deadline_retrieved'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
