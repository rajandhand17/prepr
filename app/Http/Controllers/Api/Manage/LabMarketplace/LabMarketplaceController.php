<?php

namespace App\Http\Controllers\Api\Manage\LabMarketplace;

use App\Http\Controllers\Controller;
use App\Http\Resources\Manage\Lab\LabTemplateResource;
use App\Repositories\Api\Manage\LabMarketplace\LabMarketplaceRepository;
use Illuminate\Http\Request;

class LabMarketplaceController extends Controller
{
    private LabMarketplaceRepository $labMarketplaceRepository;

    public function __construct(LabMarketplaceRepository $labMarketplaceRepository){
        $this->labMarketplaceRepository = $labMarketplaceRepository;
    }

    public function createLabMarketplace($slug){
        try {
            $checkLabExistsOrNot=$this->labMarketplaceRepository->getLabBasedOnSlug($slug);
            if (!$checkLabExistsOrNot) {
                return $this->sendError(__('responses.lab_slug_not_found'), 404);
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
