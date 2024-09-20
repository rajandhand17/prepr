<?php

namespace App\Http\Controllers\Api\Dashboard\Lab;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Dashboard\UpdateLabDashboardLayoutRequest;
use App\Http\Resources\Chat\ConversationResource;
use App\Http\Resources\Dashboard\DashboardLayoutResource;
use App\Http\Resources\Dashboard\UpComingDeadlineResource;
use App\Http\Resources\Manage\Challenge\ChallengeResource;
use App\Http\Resources\Manage\Lab\LabResource;
use App\Http\Resources\Manage\Organization\OrganizationChargebeeLimitResource;
use App\Http\Resources\Manage\ResourceModule\ResourceModuleResource;
use App\Http\Resources\Profile\FriendsResource;
use App\Http\Resources\Project\ProjectResource;
use App\Repositories\Api\Dashboard\Lab\LabDashboardRepository;
use Exception;
use Illuminate\Http\Request;

class LabDashboardController extends AppBaseController
{
    private $labDashboardRepository;

    public function __construct(LabDashboardRepository $labDashboardRepository)
    {
        $this->labDashboardRepository = $labDashboardRepository;
    }

    public function getReports(Request $request)
    {
        try {
            // Check valid request for fetching component report
            if (!in_array($request->type, ['challenges', 'labs', 'resources', 'projects'])) {
                return $this->sendError(__('responses.handler_bad_request'), 402);
            }

            // Fetch user's preferred organization
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }

            switch ($request->type) {
                case 'challenges':
                    $fetchReport = $this->labDashboardRepository->fetchChallengeReportBasedOnOrganization($organization->id, $userData);
                    $message = __('responses.retrieve_challenge_report');
                    break;
                case 'labs':
                    $fetchReport = $this->labDashboardRepository->fetchLabReportBasedOnOrganization($organization->id, $userData);
                    $message = __('responses.retrieve_lab_report');
                    break;
                case 'resources':
                    $fetchReport = $this->labDashboardRepository->fetchResourceReportBasedOnOrganization($organization->id, $userData);
                    $message = __('responses.retrieve_resource_report');
                    break;
                case 'projects':
                    $fetchReport = $this->labDashboardRepository->fetchProjectReportBasedOnOrganization($organization, $userData);
                    $message = __('responses.retrieve_project_report');
                    break;
            }
            if (!empty($fetchReport)) {
                return $this->sendResponse($fetchReport, $message, 200);
            }

            return $this->sendError(__('responses.failed_to_retrieve_report'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function subscriptionDetails()
    {
        try {
            // Fetch user's preferred organization
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }

            $checkOrganizationPlan = $this->labDashboardRepository->checkOrganizationPlan($organization->id);
            if ($checkOrganizationPlan != false) {
                return $this->sendResponse(OrganizationChargebeeLimitResource::make($organization), __('responses.organization_subscription_retrieved'));
            }

            return $this->sendError(__('responses.organization_subscription_not_retrieved'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getUpComingChallengeDeadlines()
    {
        try {
            // Fetch user's preferred organization
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }

            $fetchChallengesBasedOnOrganizationId = $this->labDashboardRepository->fetchChallengesBasedOnOrganizationId($organization->id);
            $fetchManagersUpComingDeadlineChallenges = $this->labDashboardRepository->fetchManagersUpComingDeadlineChallenges($fetchChallengesBasedOnOrganizationId);
            if ($fetchManagersUpComingDeadlineChallenges != false) {
                $response = [
                    'joined_date'   => $userData->created_at,
                    'list'          => UpComingDeadlineResource::collection($fetchManagersUpComingDeadlineChallenges),
                ];

                return $this->sendResponse($response, __('responses.manager_upcomming_deadline_retrieved'));
            }

            return $this->sendError(__('responses.not_manager_upcomming_deadline_retrieved'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getProjectsList(Request $request)
    {
        try {
            if (!in_array($request->type, ['assessment', 'submissions'])) {
                return $this->sendError(__('responses.handler_bad_request'), 402);
            }

            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }

            $fetchChallengesBasedOnOrganizationId = $this->labDashboardRepository->fetchChallengesBasedOnOrganizationId($organization->id);
            switch ($request->type) {
                case 'assessment':
                    $fetchProjectids = $this->labDashboardRepository->fetchAssessmentProjectids($fetchChallengesBasedOnOrganizationId->pluck('id'), $userData);
                    break;
                case 'submissions':
                    $fetchProjectids = $this->labDashboardRepository->fetchSubmittedProjectids($fetchChallengesBasedOnOrganizationId->pluck('id'));
                    break;
            }

            $fetchProjectList = $this->labDashboardRepository->fetchProjectList($fetchProjectids);
            if ($fetchProjectList != false) {
                $response = [
                    'total_count'  => $fetchProjectList->total(),
                    'per_page'     => $fetchProjectList->perPage(),
                    'count'        => $fetchProjectList->count(),
                    'current_page' => $fetchProjectList->currentPage(),
                    'total_pages'  => $fetchProjectList->lastPage(),
                    'list'         => ProjectResource::collection($fetchProjectList),
                ];

                return $this->sendResponse($response, __('responses.found_projects_list'));
            }

            return $this->sendError(__('responses.not_found_projects_list'), 404);
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
                    $dashboardInboxList = $this->labDashboardRepository->dashboardInboxList($userData);
                    if ($dashboardInboxList != false) {
                        return $this->sendResponse(ConversationResource::collection($dashboardInboxList), __('responses.list_conversation'), 200);
                    }
                    break;
                case 'friend':
                    $dashboardFriendList = $this->labDashboardRepository->dashboardFriendList($userData);
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

    public function getMyRecommendations(Request $request)
    {
        try {
            // Check valid request or not for my recommendations request
            if (!in_array($request->type, ['challenges', 'labs', 'resource_modules'])) {
                return $this->sendError(__('responses.handler_bad_request'), 402);
            }

            $userData = auth()->user();

            // Fetch User Skills.
            $fetchUserSkills = $this->labDashboardRepository->fetchUserSkills($userData);
            if ($fetchUserSkills == false) {
                return $this->sendError(__('responses.user_skills_not_found'));
            }

            switch ($request->type) {
                case 'challenges':
                    $fetchRecommendedChallenges = $this->labDashboardRepository->fetchRecommendedChallenges($fetchUserSkills, $userData);

                    return $this->sendResponse(ChallengeResource::collection($fetchRecommendedChallenges), __('responses.challenge_recommended_found'), 200);
                    break;
                case 'labs':
                    $fetchRecommendedLabs = $this->labDashboardRepository->fetchRecommendedLabs($fetchUserSkills, $userData);

                    return $this->sendResponse(LabResource::collection($fetchRecommendedLabs), __('responses.lab_recommended_found'), 200);
                    break;
                case 'resource_modules':
                    $fetchRecommendedResourceModules = $this->labDashboardRepository->fetchRecommendedResourceModules($fetchUserSkills, $userData);

                    return $this->sendResponse(ResourceModuleResource::collection($fetchRecommendedResourceModules), __('responses.resource_module_recommended_found'), 200);
                    break;
            }

            return $this->sendError(__('responses.failed_to_find_recommended_data'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getMyChallenges(Request $request)
    {
        try {
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }

            $challengeList = $this->labDashboardRepository->getChallengeDashboardList($request, $organization);
            if ($challengeList) {
                $response = [
                    'total_count'  => $challengeList->total(),
                    'per_page'     => $challengeList->perPage(),
                    'count'        => $challengeList->count(),
                    'current_page' => $challengeList->currentPage(),
                    'total_pages'  => $challengeList->lastPage(),
                    'list'         => ChallengeResource::collection($challengeList),
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
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }

            $labList = $this->labDashboardRepository->getLabDashboardList($request, $organization);
            if ($labList) {
                $response = [
                    'total_count'  => $labList->total(),
                    'per_page'     => $labList->perPage(),
                    'count'        => $labList->count(),
                    'current_page' => $labList->currentPage(),
                    'total_pages'  => $labList->lastPage(),
                    'list'         => LabResource::collection($labList),
                ];

                return $this->sendResponse($response, __('responses.found_labs_list'));
            }

            return $this->sendError(__('responses.not_found_labs_list'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getMyResourceModule(Request $request)
    {
        try {
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }

            $resourceModuleList = $this->labDashboardRepository->getResourceModuleDashboardList($request, $organization);
            if ($resourceModuleList) {
                $response = [
                    'total_count'  => $resourceModuleList->total(),
                    'per_page'     => $resourceModuleList->perPage(),
                    'count'        => $resourceModuleList->count(),
                    'current_page' => $resourceModuleList->currentPage(),
                    'total_pages'  => $resourceModuleList->lastPage(),
                    'list'         => ResourceModuleResource::collection($resourceModuleList),
                ];

                return $this->sendResponse($response, __('responses.found_resource_module_list'));
            }

            return $this->sendError(__('responses.not_found_resource_module_list'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fetchManagerDashboardLayout($type = null)
    {
        try {
            $message = ($type != null) ? __('responses.update_lab_dashboard_detail') : __('responses.found_lab_dashboard_detail');
            $userData = auth()->user();
            $dashboardType = 'lab';
            // Fetch the manager dashboard layout
            $fetchDashboardLayout = $this->labDashboardRepository->fetchDashboardLayout($userData, $dashboardType);

            // If layout is empty, store the static default layout
            if (!$fetchDashboardLayout || $fetchDashboardLayout->isEmpty()) {
                $fetchDashboardLayout = $this->labDashboardRepository->storeStaticDefaultLayout($userData, $dashboardType);
            }

            // Check if we have a layout and return a response
            if ($fetchDashboardLayout && $fetchDashboardLayout->isNotEmpty()) {
                $response = DashboardLayoutResource::collection($fetchDashboardLayout);

                return $this->sendResponse($response, $message, 200);
            }

            return $this->sendError(__('responses.failed_found_lab_dashboard_detail'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function updateManagerDashboardLayout(UpdateLabDashboardLayoutRequest $request)
    {
        try {
            $userData = auth()->user();
            $dashboardType = 'lab';
            $updateManagerDashboardLayout = $this->labDashboardRepository->updateDashboardLayout($request, $userData, $dashboardType);
            if ($updateManagerDashboardLayout != false) {
                return self::fetchManagerDashboardLayout('update');
            }

            return $this->sendError(__('responses.failed_update_lab_dashboard_detail'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
