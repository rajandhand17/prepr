<?php

namespace App\Http\Controllers\Api\Lab;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Lab\LabStoreRequest;
use App\Repositories\Api\Lab\LabRepository;

class LabController extends AppBaseController
{
    private $labRepository;

    public function __construct(LabRepository $labRepository)
    {
        $this->labRepository = $labRepository;
    }
    public function store(LabStoreRequest $request)
    {
    try {
        if ($request->cover_image !== null) {
            $upload_cover_image = $this->labRepository->uploadImage($request->cover_image,"lab");
            if ($upload_cover_image == false) {
                return $this->sendError(__('responses.fail_organization_image_upload'), 400);
            }
            $upload_cover_image = $upload_cover_image;
        }
        if($request->achievement_en_switch=="yes"){
            $upload_acheivements_image=$this->labRepository->uploadImage($request->achievement_image,"achievement");
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
}
