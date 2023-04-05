<?php

namespace App\Http\Controllers\Api\MemberManagement;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Repositories\Api\MemberManagement\MemberManagementInterface;
use App\Repositories\Api\MemberManagement\MemberManagementRepository;
use App\Http\Resources\MemberManagement\MemberManagementResource;

class MemberManagementController extends AppBaseController
{   
    private $MemberManagementRepository;
    public function __construct(MemberManagementRepository $MemberManagementRepository)
    {
        $this->MemberManagementRepository = $MemberManagementRepository;
    }
    public function view(Request $request)
    {  
        try {
            $member_mangement = $this->MemberManagementRepository->view($request->language);
            if($member_mangement){
                  return $this->sendResponse(MemberManagementResource::collection($member_mangement), __('responses.member_manager_found'));
            }
            return $this->sendError(__('responses.member_manager_not_found'));
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function delete($slug,Request $request)
    {
        try {
            $member_mangement=$this->MemberManagementRepository->delete($slug,$request->language);
            if($member_mangement){
                return $this->sendResponse(null, __('responses.member_manager_delete'));
               }
               return $this->sendError(__('responses.member_manager_not_delete'));
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteMultiple(Request $request)
    {
        try {
            $member_mangement=$this->MemberManagementRepository->deleteMultiple($request->slug);
            if($member_mangement){
                return $this->sendResponse(null, __('responses.member_manager_delete'));
               }
               return $this->sendError(__('responses.member_manager_not_delete'));
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
