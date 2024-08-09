<?php

namespace App\Http\Controllers\Api\Dashboard\Organization;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
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
}
