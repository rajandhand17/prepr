<?php

namespace App\Http\Controllers\Api\MemberManagement;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\MemberManagement\CreateMemberManagementRequest;
use Illuminate\Http\Request;
use App\Repositories\Api\MemberManagement\MemberManagementInterface;
use App\Repositories\Api\MemberManagement\MemberManagementRepository;
use App\Http\Resources\MemberManagement\MemberManagementResource;
use App\Http\Requests\MemberManagement\DeleteMemberManagementRequest;
use App\Http\Requests\MemberManagement\UploadCsvFileMemberManagementRequest;

class MemberManagementController extends AppBaseController
{   
    private $memberManagementRepository;
    public function __construct(MemberManagementRepository $memberManagementRepository)
    {
        $this->memberManagementRepository = $memberManagementRepository;
    }
    
    public function index($component,$slug,Request $request)
    {     
        try {
            $member_mangement = $this->memberManagementRepository->index($component,$slug,$request);
            if($member_mangement){
              return $this->sendResponse(MemberManagementResource::collection($member_mangement), __('responses.member_manager_found'));
            }
            return $this->sendError(__('responses.member_manager_not_found'),500);
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function delete($component,$slug,Request $request)
    {   
        try {
            $member_mangement=$this->memberManagementRepository->delete($component,$slug,$request);
            if($member_mangement){
                return $this->sendResponse(null, __('responses.member_manager_delete'));
               }
               return $this->sendError(__('responses.member_manager_not_delete'),500);
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create($component,$slug,Request $request)
    {
        try {
            $member_mangement=$this->memberManagementRepository->create($component,$slug,$request);
            if($member_mangement){
                return $this->sendResponse(null, __('responses.create_member_manger_success'));
               }
               return $this->sendError(__('responses.member_manager_not_delete'));
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    // public function uploadCsv($component,$slug,UploadCsvFileMemberManagementRequest $request)
    // { 
    //     try {
    //         $member_mangement=$this->memberManagementRepository->uploadCsv($component,$slug,$request);
    //         if($member_mangement){
    //             return $this->sendResponse(null, __('responses.create_member_manger_csv_success'));
    //            }
    //            return $this->sendError(__('responses.create_member_manger_csv_failed'));
    //     } catch (\Exception $e) {
    //         return $this->sendError(__('responses.send_error'), 500);
    //     }
    // }
}
