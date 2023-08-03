<?php

namespace App\Http\Controllers\Api\public\Organization;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\Organization\OrganizationResource as PublicOrganizationResource;
use App\Repositories\Api\Public\Organization\OrganizationRepository;
use Illuminate\Http\Request;

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
            $organization = $this->organizationRepository->getOrganizationList($request);
            if ($organization !== false) {
                $response = [
                    'total_count'  => $organization->total(),
                    'per_page'     => $organization->perPage(),
                    'count'        => $organization->count(),
                    'current_page' => $organization->currentPage(),
                    'total_pages'  => $organization->lastPage(),
                    'list'         => PublicOrganizationResource::collection($organization),
                ];

                return $this->sendResponse([$response], __('responses.found_organization_list'));
            }

            return $this->sendError(__('responses.not_found_organization_list'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if ($organization) {
                return $this->sendResponse(PublicOrganizationResource::make($organization), __('responses.found_organization_list'));
            }

            return $this->sendError(__('responses.organization_not_exists'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
    public  function organizationSocialActivitiesService($slug, $action){
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if ($organization !== null) {
                $checkOrganization = $this->organizationRepository->checkExists($organization->id, $action);
                if ($checkOrganization !== false && isset($checkOrganization->id)) {
                    return $this->sendError(__('responses.already_followed_organization'), 400);
                }
                $organization = $this->organizationRepository->organizationSocialActivitiesService($organization->id,$checkOrganization['column'],$checkOrganization['action']);
                if ($organization) {
                    return $this->sendResponse([], __('responses.follow_organization_successfully'));
                }
            }
            return $this->sendError(__('responses.organization_not_exists'), 404);
            } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
