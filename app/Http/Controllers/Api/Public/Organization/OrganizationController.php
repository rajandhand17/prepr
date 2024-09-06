<?php

namespace App\Http\Controllers\Api\Public\Organization;

use App\Helpers\ChargebeeHelper;
use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\Organization\OrganizationDetailResource;
use App\Http\Resources\Public\Organization\OrganizationResource;
use App\Repositories\Api\Public\Organization\OrganizationRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizationController extends AppBaseController
{
    private $organizationRepository;

    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    public function index(Request $request)
    {
        try {
            $organizations = $this->organizationRepository->getList($request);
            if ($organizations !== false) {
                $response = [
                    'total_count'  => $organizations->total(),
                    'per_page'     => $organizations->perPage(),
                    'count'        => $organizations->count(),
                    'current_page' => $organizations->currentPage(),
                    'total_pages'  => $organizations->lastPage(),
                    'list'         => OrganizationResource::collection($organizations),
                ];

                return $this->sendResponse($response, __('responses.found_organization_list'));
            }

            return $this->sendError(__('responses.not_found_organization_list'), 404);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if ($organization) {
                $this->organizationRepository->incrementView($organization);

                return $this->sendResponse(OrganizationDetailResource::make($organization), __('responses.found_organization_list'));
            }

            return $this->sendError(__('responses.organization_not_exists'), 404);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function plansDetail()
    {
        try {
            $planData = ChargebeeHelper::getAllPlanDetailsAndLimits();
            if ($planData) {
                return $this->sendResponse($planData, __('responses.plan_details_retrived'));
            }

            return $this->sendError(__('responses.plan_not_retrived'), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function socialActivity($slug, $action)
    {
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if ($organization !== null) {
                $getColumnNameValue = $this->organizationRepository->getColumnNameValue($action);
                if (!$getColumnNameValue) {
                    return $this->sendError(__('responses.handler_bad_request'), 400);
                }
                $checkActivity = $this->organizationRepository->checkSocialActivity($organization->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                $action = str_replace('-', '_', $action);
                if ($checkActivity === true) {
                    return $this->sendError(__('responses.already_'.$action.'_organization'), 400);
                }
                $organization = $this->organizationRepository->captureSocialActivity($organization->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                if ($organization) {
                    return $this->sendResponse([], __('responses.'.$action.'_organization_successfully'));
                }
            }

            return $this->sendError(__('responses.organization_not_exists'), 404);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @param string $slug
     */
    public function organizationMemberActivity(string $slug)
    {
        try {
            $lab = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if ($lab) {
                $data = $this->organizationRepository->organizationMemberActivity($lab);
                if ($data === false) {
                    return $this->sendError(__('Failed to fetch organization member activity.'), Response::HTTP_BAD_REQUEST);
                }

                return $this->sendResponse($data, __('Organization member progress.'));
            }

            return $this->sendError(__('responses.organization_slug_not_found'), 404);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('Failed to fetch organization member activity.'), Response::HTTP_BAD_REQUEST);
        }
    }
}
