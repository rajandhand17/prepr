<?php

namespace App\Http\Controllers\Api\Dashboard\User;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Dashboard\UpdateUserDashboardLayoutRequest;
use App\Http\Resources\Chat\ConversationResource;
use App\Http\Resources\Dashboard\ChallengeDashboardResource;
use App\Http\Resources\Dashboard\ChallengePathDashboardResource;
use App\Http\Resources\Dashboard\DashboardLayoutResource;
use App\Http\Resources\Dashboard\LabDashboardResource;
use App\Http\Resources\Dashboard\LabProgramDashboardResource;
use App\Http\Resources\Dashboard\ProjectDashboardResource;
use App\Http\Resources\Dashboard\ResourceCollectionDashboardResource;
use App\Http\Resources\Dashboard\ResourceGroupDashboardResource;
use App\Http\Resources\Dashboard\ResourceModuleDashboardResource;
use App\Http\Resources\Dashboard\UpComingDeadlineResource;
use App\Http\Resources\Profile\FriendsResource;
use App\Http\Resources\Project\ProjectResource;
use App\Http\Resources\Public\Achievement\AchievementResource;
use App\Http\Resources\Public\Challenge\ChallengeResource;
use App\Http\Resources\Public\Lab\LabResource;
use App\Http\Resources\Public\ResourceModule\ResourceModuleResource;
use App\Repositories\Api\Dashboard\User\UserDashboardRepository;
use App\Services\ProjectService;
use App\Services\Public\ChallengePathService;
use App\Services\Public\ChallengeService;
use App\Services\Public\LabProgramService;
use App\Services\Public\LabService;
use App\Services\Public\ResourceCollectionService;
use App\Services\Public\ResourceGroupService;
use App\Services\Public\ResourceModuleService;
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
                    $inviteStatus = config('constants.member_management_invite_status.pending');
                    $challengeIds = $this->userDashboardRepository->challengeRequestIds($userData, $inviteStatus);
                    break;
                case 'favourite':
                    $challengeIds = $this->userDashboardRepository->challengeFavouriteIds($userData);
                    break;
            }

            $challenges = $this->userDashboardRepository->getChallengeDashboardList($challengeIds);
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
                    $inviteStatus = config('constants.member_management_invite_status.pending');
                    $labIds = $this->userDashboardRepository->labRequestIds($userData, $inviteStatus);
                    break;
                case 'favourite':
                    $labIds = $this->userDashboardRepository->labFavouriteIds($userData);
                    break;
            }

            $labs = $this->userDashboardRepository->getLabDashboardList($labIds);
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
                    $inviteStatus = config('constants.project_member_management_invite_status.pending');
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

                    return $this->sendResponse(ResourceModuleResource::collection($fetchRecommendedResourceModules), __('responses.resource_module_recommended_found'), 200);
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
            if ($fetchUpComingDeadlineChallenges != false) {
                $response = [
                    'joined_date'   => $userData->created_at,
                    'list'          => UpComingDeadlineResource::collection($fetchUpComingDeadlineChallenges),
                ];

                return $this->sendResponse($response, __('responses.user_upcomming_deadline_retrieved'));
            }

            return $this->sendError(__('responses.not_user_upcomming_deadline_retrieved'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getInboxFriendRequests(Request $request)
    {
        try {
            // Check valid request or not for inbox and friend request
            if (!in_array($request->type, ['inbox', 'friend'])) {
                return $this->sendError(__('responses.handler_bad_request'), 402);
            }

            // User's chat and friends pending request
            $userData = auth()->user();
            switch ($request->type) {
                case 'inbox':
                    $dashboardInboxList = $this->userDashboardRepository->dashboardInboxList($userData);
                    if ($dashboardInboxList != false) {
                        return $this->sendResponse(ConversationResource::collection($dashboardInboxList), __('responses.list_conversation'), 200);
                    }
                    break;
                case 'friend':
                    $dashboardFriendList = $this->userDashboardRepository->userDashboardFriendList($userData);
                    if ($dashboardFriendList != false) {
                        return $this->sendResponse(FriendsResource::collection($dashboardFriendList), __('responses.friends_listing_retrieved'));
                    }
                    break;
            }

            return $this->sendError(__('responses.inbox_friends_listing_retrieved'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getLastVisitedModule()
    {
        try {
            $userData = auth()->user();
            // Fetch lasted visited component
            $fetchLastVisited = $this->userDashboardRepository->fetchLastVisited($userData);
            if ($fetchLastVisited != false) {
                // Based on module type fetch component data
                switch ($fetchLastVisited->module_type) {
                    case '0':
                        $fetchComponentData = LabService::getLabBasedOnId($fetchLastVisited->module_id);
                        if ($fetchComponentData) {
                            return $this->sendResponse(LabDashboardResource::make($fetchComponentData), __('responses.last_visited_lab'), 200);
                        }
                        break;
                    case '1':
                        $fetchComponentData = LabProgramService::getLabProgramBasedOnId($fetchLastVisited->module_id);
                        if ($fetchComponentData) {
                            return $this->sendResponse(LabProgramDashboardResource::make($fetchComponentData), __('responses.last_visited_lab_program'), 200);
                        }
                        break;
                    case '2':
                        $fetchComponentData = ChallengeService::getChallengeBasedOnId($fetchLastVisited->module_id);
                        if ($fetchComponentData) {
                            return $this->sendResponse(ChallengeDashboardResource::make($fetchComponentData), __('responses.last_visited_challenge'), 200);
                        }
                        break;
                    case '3':
                        $fetchComponentData = ChallengePathService::getChallengePathBasedOnId($fetchLastVisited->module_id);
                        if ($fetchComponentData) {
                            return $this->sendResponse(ChallengePathDashboardResource::make($fetchComponentData), __('responses.last_visited_challenge'), 200);
                        }
                        break;
                    case '4':
                        $fetchComponentData = ResourceModuleService::getResourceModuleBasedOnId($fetchLastVisited->module_id);
                        if ($fetchComponentData) {
                            return $this->sendResponse(ResourceModuleDashboardResource::make($fetchComponentData), __('responses.last_visited_resource_module'), 200);
                        }
                        break;
                    case '5':
                        $fetchComponentData = ResourceCollectionService::getResourceCollectionBasedOnId($fetchLastVisited->module_id);
                        if ($fetchComponentData) {
                            return $this->sendResponse(ResourceCollectionDashboardResource::make($fetchComponentData), __('responses.last_visited_resource_collection'), 200);
                        }
                        break;
                    case '6':
                        $fetchComponentData = ResourceGroupService::getResourceGroupBasedOnId($fetchLastVisited->module_id);
                        if ($fetchComponentData) {
                            return $this->sendResponse(ResourceGroupDashboardResource::make($fetchComponentData), __('responses.last_visited_resource_group'), 200);
                        }
                        break;
                    case '7':
                        $fetchComponentData = ProjectService::getProjectBasedOnId($fetchLastVisited->module_id);
                        if ($fetchComponentData) {
                            return $this->sendResponse(ProjectDashboardResource::make($fetchComponentData), __('responses.last_visited_project'), 200);
                        }
                        break;
                }
            }

            return $this->sendError(__('responses.last_visited_not_found'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fetchUserDashboardLayout($type = null)
    {
        try {
            $message = ($type != null) ? __('responses.update_user_dashboard_detail') : __('responses.found_user_dashboard_detail');
            $userData = auth()->user();
            $dashboardType = 'user';
            // Fetch the user dashboard layout
            $fetchUserDashboardLayout = $this->userDashboardRepository->fetchUserDashboardLayout($userData, $dashboardType);

            // If layout is empty, store the static default layout
            if (!$fetchUserDashboardLayout || $fetchUserDashboardLayout->isEmpty()) {
                $fetchUserDashboardLayout = $this->userDashboardRepository->storeStaticDefaultLayout($userData, $dashboardType);
            }

            // Check if we have a layout and return a response
            if ($fetchUserDashboardLayout && $fetchUserDashboardLayout->isNotEmpty()) {
                $response = DashboardLayoutResource::collection($fetchUserDashboardLayout);

                return $this->sendResponse($response, $message, 200);
            }

            return $this->sendError(__('responses.failed_found_user_dashboard_detail'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function updateUserDashboardLayout(UpdateUserDashboardLayoutRequest $request)
    {
        try {
            $userData = auth()->user();
            $dashboardType = 'user';
            $updateUserDashboardLayout = $this->userDashboardRepository->updateUserDashboardLayout($request, $userData, $dashboardType);
            if ($updateUserDashboardLayout != false) {
                return self::fetchUserDashboardLayout('update');
            }

            return $this->sendError(__('responses.failed_update_user_dashboard_detail'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
