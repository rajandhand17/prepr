<?php

namespace App\Http\Controllers\Api\Lab;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Lab\LabStoreRequest;
use App\Repositories\Api\Lab\LabRepository;
use App\Repositories\Api\LabAcheivement\LabAcheivementRepository;
use Illuminate\Http\Request;

class LabController extends AppBaseController
{
    private $labRepository;
    private $labAcheivementRepository;

    public function __construct(LabRepository $labRepository,LabAcheivementRepository $labAcheivementRepository)
    {
        $this->labRepository = $labRepository;
        $this->labAcheivementRepository = $labAcheivementRepository;
    }
    public function store(LabStoreRequest $request)
    {
    try {
        if ($request->cover_image !== null) {
            $upload_cover_image = $this->labRepository->uploadCoverImage($request->cover_image);
            if ($upload_cover_image == false) {
                return $this->sendError(__('responses.fail_organization_image_upload'), 400);
            }
            $upload_cover_image = $upload_cover_image;
        }
        if($request->is_achievement_enabled=="yes"){
            $upload_acheivements_image=$this->labAcheivementRepository->uploadAcheivementImage($request->achievement_image);
            if ($upload_acheivements_image == false) {
                return $this->sendError(__('responses.fail_organization_image_upload'), 400);
            }
            $upload_acheivements_image = $upload_acheivements_image;
        }else{
            $upload_acheivements_image=null;
        }
        $component="lab";
        $store = $this->labRepository->store($component,$request,$upload_cover_image,$upload_acheivements_image);
        if ($store!=false) {

            return $this->sendResponse([],__('responses.lab_stored_success'),200);
        }
        return $this->sendError(__('responses.lab_stored_failed'), 400);
    } catch (\Exception $e) {
        return $this->sendError(__('responses.send_error'), 500);
    }
    }

    public function index(Request $request){
        try {
            $lab = $this->labRepository->getLabList($request);
            if ($lab !== false) {
                return $lab;
            }

            return $this->sendError(__('responses.organization_view_get_failed'), 400);
        } catch(\Exception $e) {
             return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
