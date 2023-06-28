<?php

namespace App\Repositories\Api\Lab;
use App\Services\LabService;
use App\Models\Lab;
use App\Services\MemberManagementService;

class LabRepository implements LabInterface
{
    private $LabService;
    private $memberManagementService;

    public function __construct(LabService $LabService, MemberManagementService $memberManagementService)
    {
        $this->LabService = $LabService;
        $this->memberManagementService=$memberManagementService;
    }

    public function uploadImage($image,$type){
        try {
            return $this->LabService->uploadImage($image,$type);
        } catch (\Exception $e){
            return false;
        }
    }
    public function store($component,$request,$upload_profile_image,$upload_acheivements_image)
    {
        try {
            $addLab=$this->LabService->store($request,$upload_profile_image,$upload_acheivements_image);
            if($addLab!==false){
                $memberList = [];
            if ($request->invite_type == 'csv') {
                $memberList = $this->memberManagementService->getRecordsFromCsv($request);
                if (!$memberList && !count($memberList) > 0) {
                    return false;
                }
            }
            if ($request->invite_type == 'email') {
                $memberList = $this->memberManagementService->getRecordsFromEmailArray($request);
                if (!$memberList && !count($memberList) > 0) {
                    return false;
                }
            }
            if (is_array($memberList) && count($memberList) > 0) {
                $checkStatus = $this->memberManagementService->addMembers($addLab, $component, $request, $memberList);
                if ($checkStatus != false) {
                    return true;
                }

                return false;
            }
            }
        
        } catch (\Exception $e) {
            return false;
        }
    }

}
