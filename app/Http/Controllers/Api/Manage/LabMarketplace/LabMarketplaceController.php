<?php

namespace App\Http\Controllers\Api\Manage\LabMarketplace;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Manage\Lab\LabResource;
use App\Http\Resources\Manage\LabMarketplace\LabMarketplaceResource;
use App\Repositories\Api\Manage\LabMarketplace\LabMarketplaceRepository;
use App\Services\Manage\OrganizationService;
use Exception;
use Illuminate\Http\Request;

class LabMarketplaceController extends AppBaseController
{
    private LabMarketplaceRepository $labMarketplaceRepository;

    public function __construct(LabMarketplaceRepository $labMarketplaceRepository)
    {
        $this->labMarketplaceRepository = $labMarketplaceRepository;
    }

    public function index(Request $request)
    {
        try {
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_found'), 404);
            }

            $labMarketplace = $this->labMarketplaceRepository->getLabMarketPlaceList($request);
            if ($labMarketplace) {
                $response = [
                    'total_count'  => $labMarketplace->total(),
                    'per_page'     => $labMarketplace->perPage(),
                    'count'        => $labMarketplace->count(),
                    'current_page' => $labMarketplace->currentPage(),
                    'total_pages'  => $labMarketplace->lastPage(),
                    'list'         => LabMarketplaceResource::collection($labMarketplace),
                ];

                return $this->sendResponse($response, __('responses.found_lab_marketplace_list'));
            }

            return $this->sendError(__('responses.not_found_lab_marketplace_list'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addLabToMarketplace($slug)
    {
        try {
            $checkLabExistsOrNot = $this->labMarketplaceRepository->getLabBasedOnSlug($slug);
            if (!$checkLabExistsOrNot) {
                return $this->sendError(__('responses.lab_not_found'), 404);
            }
            
            $checkLabMarketplace = $this->labMarketplaceRepository->getCheckLabUuid($checkLabExistsOrNot->uuid);
            if ($checkLabMarketplace) {
                return $this->sendError(__('responses.lab_already_cloned'), 422);
            }

            $labMarketplace = $this->labMarketplaceRepository->addLabToMarketplace($slug, $checkLabExistsOrNot->id);
            if ($labMarketplace) {
                return $this->sendResponse(LabMarketplaceResource::make($labMarketplace), __('responses.lab_marketplace_stored_success'), 200);
            }

            return $this->sendError(__('responses.lab_marketplace_stored_failed'), 400);
        } catch(Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug, Request $request)
    {
        try {
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_found'), 404);
            }

            $labMarketplace = $this->labMarketplaceRepository->getLabMarketplaceBasedOnSlug($slug);
            if ($labMarketplace) {
                return $this->sendResponse(LabMarketplaceResource::make($labMarketplace), __('responses.lab_marketplace_found'));
            }

            return $this->sendError(__('responses.lab_marketplace_not_exists'), 404);
        } catch(Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteLabMarketplace($slug)
    {
        try {
            $checkLabMarketplaceExists = $this->labMarketplaceRepository->getLabMarketplaceBasedOnSlug($slug);
            if (!$checkLabMarketplaceExists) {
                return $this->sendError(__('responses.lab_marketplace_not_exists'), 404);
            }
            $deleteLabMarketplace = $this->labMarketplaceRepository->deleteLabMarketplace($slug, $checkLabMarketplaceExists->id);
            if ($deleteLabMarketplace) {
                return $this->sendResponse(null, __('responses.lab_marketplace_deleted_successfully'));
            }

            return $this->sendError(__('responses.lab_marketplace_deleted_failed'), 402);
        } catch(Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function redeemLab($slug, Request $request)
    {
        try {
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_found'), 404);
            }

            $labMarketplace = $this->labMarketplaceRepository->getLabMarketplaceBasedOnSlug($slug);
            if (!$labMarketplace) {
                return $this->sendError(__('responses.lab_marketplace_not_exists'), 404);
            }

            $checkLabRedeemedOrNot = $this->labMarketplaceRepository->checkLabRedeemedOrNot($labMarketplace->id, $organization->id);
            if (!$checkLabRedeemedOrNot) {
                return $this->sendError(__('responses.lab_marketplace_already_redeemed'), 404);
            }

            $labRedeem = $this->labMarketplaceRepository->labRedeem($labMarketplace->id, $organization->id);
            if ($labRedeem) {
                return $this->sendResponse(LabResource::make($labRedeem), __('responses.lab_marketplace_redeemed'), 200);
            }

            return $this->sendError(__('responses.lab_marketplace_not_redeemed'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
