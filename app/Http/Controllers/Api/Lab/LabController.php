<?php

namespace App\Http\Controllers\Api\Lab;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Lab\DeleteLabRequest;
use App\Http\Requests\Lab\LabStoreRequest;
use App\Http\Resources\Lab\LabResource;
use App\Repositories\Api\Lab\LabRepository;
use App\Repositories\Api\LabAcheivement\LabAcheivementRepository;
use Illuminate\Http\Request;
use App\Helpers\UtilityHelper;
use App\Http\Requests\Lab\LabUpdateRequest;

class LabController extends AppBaseController
{
    private $labRepository;
    private $labAcheivementRepository;

    public function __construct(LabRepository $labRepository, LabAcheivementRepository $labAcheivementRepository)
    {
        $this->labRepository = $labRepository;
        $this->labAcheivementRepository = $labAcheivementRepository;
    }

    public function index(Request $request)
    {
        try {
            $lab = $this->labRepository->getLabList($request);
            if ($lab !== false) {
                return $this->sendResponse(LabResource::collection($lab), 'Labs fetched successfully');
            }

            return $this->sendError('Labs not found', 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function labProgram(Request $request){
        try {
            $labProgram = $this->labRepository->getLabProgramList($request);
            if ($labProgram !== false) {
                return $this->sendResponse(LabResource::collection($labProgram), 'Labs fetched successfully');
            }
            return $this->sendError('Labs not found', 400);
        } catch (\Exception $e) {
        return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function store(LabStoreRequest $request)
    {
        try {
            $upload_cover_image = null;
            $upload_acheivements_image = null;
            
            if ($request->cover_image !== null) {
                $upload_cover_image = $this->labRepository->uploadCoverImage($request->cover_image);
                if ($upload_cover_image == false) {
                    return $this->sendError(__('responses.fail_organization_image_upload'), 400);
                }
                $upload_cover_image = $upload_cover_image;
            }
            if ($request->is_achievement_enabled == 'yes') {
                $upload_acheivements_image = $this->labAcheivementRepository->uploadAcheivementImage($request->achievement_image);
                if ($upload_acheivements_image == false) {
                    return $this->sendError(__('responses.fail_organization_image_upload'), 400);
                }
                $upload_acheivements_image = $upload_acheivements_image;
            }
            $createdLab = $this->labRepository->createLab($request, $upload_cover_image, $upload_acheivements_image);

            if ($createdLab != false) {
                return $this->sendResponse(LabResource::make($createdLab), __('responses.lab_stored_success'), 200);
            }

            return $this->sendError(__('responses.lab_stored_failed'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $lab = $this->labRepository->checkSlug($slug);
            if ($lab == false) {
                return $this->sendError(__('responses.lab_slug_not_exists'), 400);
            }
            $labDetails = $this->labRepository->getLabDetails($slug);
            if ($labDetails) {
                return $this->sendResponse(LabResource::make($labDetails), __('responses.lab_found'), 200);
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
    public function update($slug,LabUpdateRequest $request){
        try {
            $checkComponentBasedOnSlug = UtilityHelper::checkComponentSlugExistOrNot("lab", $slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(ucfirst("lab").' Not Found', 403);
            }
            $upload_cover_image = null;
            $upload_acheivements_image = null;
            if ($request->cover_image !== null) {
                $upload_cover_image = $this->labRepository->updateCoverImage($request->cover_image);
                if ($upload_cover_image == false) {
                    return $this->sendError(__('responses.fail_organization_image_upload'), 400);
                }
                $upload_cover_image = $upload_cover_image;
            }
            if ($request->is_achievement_enabled == 'yes') {
                $upload_acheivements_image = $this->labAcheivementRepository->uploadAcheivementImage($request->achievement_image);
                if ($upload_acheivements_image == false) {
                    return $this->sendError(__('responses.fail_organization_image_upload'), 400);
                }
                $upload_acheivements_image = $upload_acheivements_image;
            }
            $updateLab=$this->labRepository->updateLab($checkComponentBasedOnSlug->id,$request, $upload_cover_image, $upload_acheivements_image);
            if ($updateLab != false) {
                return $this->sendResponse(LabResource::make($updateLab), __('responses.lab_update'), 200);
            }
            return $this->sendError(__('responses.lab_not_update'));
        } catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function delete($slug,Request $request){
        try { 
            $checkComponentBasedOnSlug = UtilityHelper::checkComponentSlugExistOrNot("lab", $slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(ucfirst("lab").' Not Found', 403);
            }
            $lab=$this->labRepository->deleteLab($checkComponentBasedOnSlug->id,$request);
            if ($lab) {
                return $this->sendResponse(null, __('responses.lab_delete'));
            }

            return $this->sendError(__('responses.lab_not_delete'), 400);
        } catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkSlug($slug)
    {
        try {
            $checkLabSlugExistsOrNot = $this->labRepository->checkSlug($slug);
            if ($checkLabSlugExistsOrNot == false) {
                return $this->sendResponse([], __('responses.lab_slug_not_exists'), 200);
            }

            return $this->sendError(__('responses.lab_slug_exists'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkName($title)
    {
        try {
            $checkLabNameExistsOrNot = $this->labRepository->checkNameExistsOrNot($title);
            if ($checkLabNameExistsOrNot) {
                return $this->sendError(__('responses.lab_name_not_exists'));
            }
            return $this->sendResponse([], __('responses.lab_name_exists'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }



    public function labActivity($activity,$slug,Request $request)
    {
        try {
            /**checking slug that is exists or not */
            $checkLabSlugExistsOrNot = $this->labRepository->checkSlug($slug);
            if ($checkLabSlugExistsOrNot == false) {
                return $this->sendError(__('responses.lab_slug_exists'), 403);
            }
            $checkActivityAlreadyExistOrNot = $this->labRepository->checkActivity($activity,$checkLabSlugExistsOrNot->id);
            if ($checkActivityAlreadyExistOrNot == false) {
                return $this->sendError(__('responses.lab_already_done_activity').$activity, 400);
            } 
            $checkLabActivity = $this->labRepository->storeLabActivity($activity,$checkLabSlugExistsOrNot->id,$request);
            if ($checkLabActivity){
                return $this->sendResponse([],__('responses.lab_activity_successfully'), 200);
            }
            return $this->sendError(__('responses.send_error'),500);
        } catch (\Exception $e){
            dd($e);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

}
