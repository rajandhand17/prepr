<?php

namespace App\Http\Controllers\Api\Manage\LabMarketplace;

use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Manage\LabMarketplace\LabMarketplaceRepository;

class LabMarketplaceController extends AppBaseController
{
    private LabMarketplaceRepository $labMarketplaceRepository;

    public function __construct(LabMarketplaceRepository $labMarketplaceRepository){
        $this->labMarketplaceRepository = $labMarketplaceRepository;
    }

    public function createLabMarketplace($slug){
        try {
            $checkLabExistsOrNot=$this->labMarketplaceRepository->getLabBasedOnSlug($slug);
            if (!$checkLabExistsOrNot){
                return $this->sendError(__('responses.lab_slug_not_found'), 404);
            }
            $checkLabMarketplace=$this->labMarketplaceRepository->getCheckUuid($checkLabExistsOrNot->uuid);
            if ($checkLabMarketplace) {
                return $this->sendError(__('responses.already_cloned'),200);
            }
            $labMarketplace=$this->labMarketplaceRepository->createLabMarketplace($slug,$checkLabExistsOrNot);
            if ($labMarketplace) {
                return $this->sendResponse($labMarketplace, __('responses.template_lab_stored_success'), 200);
            }
            return $this->sendError(__('responses.template_lab_stored_failed'), 400);
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
