<?php

namespace App\Http\Controllers\Api\Manage\LabMarketplace;

use App\Helpers\ChargebeeHelper;
use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Manage\Lab\LabResource;
use App\Http\Resources\Manage\LabMarketplace\LabMarketplaceResource;
use App\Repositories\Api\Manage\LabMarketplace\LabMarketplaceRepository;
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
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            $request->merge(['organization_id' => $organization->id]);

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
            UtilityHelper::logError($e);

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

            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }

            if ($checkLabExistsOrNot->organization_id != $organization->id) {
                return $this->sendError(__('responses.lab_switcher_error'), 403);
            }

            if ($checkLabExistsOrNot->is_accessible == '0') {
                return $this->sendError(__('responses.lab_not_accessible'), 403);
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
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $labMarketplace = $this->labMarketplaceRepository->getLabMarketplaceBasedOnSlug($slug);
            if ($labMarketplace) {
                return $this->sendResponse(LabMarketplaceResource::make($labMarketplace), __('responses.lab_marketplace_found'));
            }

            return $this->sendError(__('responses.lab_marketplace_not_exists'), 404);
        } catch(Exception $e) {
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function redeemLab($slug)
    {
        try {
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }

            // checks creation limits of the Lab
            $checkLabLimit = ChargebeeHelper::checkComponentLimitBasedOnOrganization($organization->id, 'preBuildLab');
            if ($checkLabLimit['fetchOrganizationPlanDetails'] !== 'Unlimited') {
                $checkLabCount = $this->labMarketplaceRepository->getLabRedeemCountBasedOnOrganization($checkLabLimit['organizationId']);
                if ($checkLabLimit['fetchOrganizationPlanDetails'] <= $checkLabCount) {
                    return $this->sendError(__('responses.reached_redeem_lab_limit'), 400);
                }
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
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
