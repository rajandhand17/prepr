<?php

namespace App\Http\Controllers\Api\Dashboard\Organization;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Chat\ConversationResource;
use App\Http\Resources\Dashboard\UpComingDeadlineResource;
use App\Http\Resources\Manage\Challenge\ChallengeResource;
use App\Http\Resources\Manage\Lab\LabResource;
use App\Http\Resources\Manage\Organization\OrganizationChargebeeLimitResource;
use App\Http\Resources\Manage\Organization\OrganizationResource;
use App\Http\Resources\Manage\ResourceModule\ResourceModuleResource;
use App\Http\Resources\Profile\FriendsResource;
use App\Http\Resources\Project\ProjectResource;
use App\Repositories\Api\Dashboard\Organization\OrganizationDashboardRepository;
use Exception;
use Illuminate\Http\Request;

class OrganizationDashboardController extends AppBaseController
{
    private $organizationDashboardRepository;

    public function __construct(OrganizationDashboardRepository $organizationDashboardRepository)
    {
        $this->organizationDashboardRepository = $organizationDashboardRepository;
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
                    $fetchReport = $this->organizationDashboardRepository->fetchChallengeReportBasedOnOrganization($organization->id);
                    $message = __('responses.retrieve_challenge_report');
                    break;
                case 'labs':
                    $fetchReport = $this->organizationDashboardRepository->fetchLabReportBasedOnOrganization($organization->id);
                    $message = __('responses.retrieve_lab_report');
                    break;
                case 'resources':
                    $fetchReport = $this->organizationDashboardRepository->fetchResourceReportBasedOnOrganization($organization->id);
                    $message = __('responses.retrieve_resource_report');
                    break;
                case 'projects':
                    $fetchReport = $this->organizationDashboardRepository->fetchProjectReportBasedOnOrganization($organization);
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

            $checkOrganizationPlan = $this->organizationDashboardRepository->checkOrganizationPlan($organization->id);
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

            $fetchChallengesBasedOnOrganizationId = $this->organizationDashboardRepository->fetchChallengesBasedOnOrganizationId($organization->id);
            $fetchManagersUpComingDeadlineChallenges = $this->organizationDashboardRepository->fetchManagersUpComingDeadlineChallenges($fetchChallengesBasedOnOrganizationId);
            if (!empty($fetchManagersUpComingDeadlineChallenges)) {
                return $this->sendResponse(UpComingDeadlineResource::collection($fetchManagersUpComingDeadlineChallenges), __('responses.manager_upcomming_deadline_retrieved'), 200);
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

            $fetchChallengesBasedOnOrganizationId = $this->organizationDashboardRepository->fetchChallengesBasedOnOrganizationId($organization->id);
            switch ($request->type) {
                case 'assessment':
                    $fetchProjectids = $this->organizationDashboardRepository->fetchAssessmentProjectids($fetchChallengesBasedOnOrganizationId->pluck('id'), $userData);
                    break;
                case 'submissions':
                    $fetchProjectids = $this->organizationDashboardRepository->fetchSubmittedProjectids($fetchChallengesBasedOnOrganizationId->pluck('id'));
                    break;
            }

            $fetchProjectList = $this->organizationDashboardRepository->fetchProjectList($fetchProjectids);
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
                    $dashboardInboxList = $this->organizationDashboardRepository->dashboardInboxList($userData);
                    if ($dashboardInboxList != false) {
                        return $this->sendResponse(ConversationResource::collection($dashboardInboxList), __('responses.list_conversation'), 200);
                    }
                    break;
                case 'friend':
                    $dashboardFriendList = $this->organizationDashboardRepository->dashboardFriendList($userData);
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
            $fetchUserSkills = $this->organizationDashboardRepository->fetchUserSkills($userData);
            if ($fetchUserSkills == false) {
                return $this->sendError(__('responses.user_skills_not_found'));
            }

            switch ($request->type) {
                case 'challenges':
                    $fetchRecommendedChallenges = $this->organizationDashboardRepository->fetchRecommendedChallenges($fetchUserSkills, $userData);

                    return $this->sendResponse(ChallengeResource::collection($fetchRecommendedChallenges), __('responses.challenge_recommended_found'), 200);
                    break;
                case 'labs':
                    $fetchRecommendedLabs = $this->organizationDashboardRepository->fetchRecommendedLabs($fetchUserSkills, $userData);

                    return $this->sendResponse(LabResource::collection($fetchRecommendedLabs), __('responses.lab_recommended_found'), 200);
                    break;
                case 'resource_modules':
                    $fetchRecommendedResourceModules = $this->organizationDashboardRepository->fetchRecommendedResourceModules($fetchUserSkills, $userData);

                    return $this->sendResponse(ResourceModuleResource::collection($fetchRecommendedResourceModules), __('responses.lab_recommended_found'), 200);
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

            $challengeList = $this->organizationDashboardRepository->getChallengeList($request, $organization);
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

            $labList = $this->organizationDashboardRepository->getLabList($request, $organization);
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

            $resourceModuleList = $this->organizationDashboardRepository->getResourceModuleList($request, $organization);
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

    public function getMyOrganization(Request $request)
    {
        try {
            $userData = auth()->user();
            $fetchOrganizationIds = $this->organizationDashboardRepository->fetchOrganizationIds($userData);
            $organizationList = $this->organizationDashboardRepository->fetchOrganizations($request, $fetchOrganizationIds);
            if ($organizationList) {
                $response = [
                    'total_count'  => $organizationList->total(),
                    'per_page'     => $organizationList->perPage(),
                    'count'        => $organizationList->count(),
                    'current_page' => $organizationList->currentPage(),
                    'total_pages'  => $organizationList->lastPage(),
                    'list'         => OrganizationResource::collection($organizationList),
                ];

                return $this->sendResponse($response, __('responses.found_organization_list'));
            }

            return $this->sendError(__('responses.not_found_organization_list'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
