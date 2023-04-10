<?php

namespace App\Http\Controllers\Api\MemberManagement;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Repositories\Api\MemberManagement\MemberManagementInterface;
use App\Repositories\Api\MemberManagement\MemberManagementRepository;
use App\Http\Resources\MemberManagement\MemberManagementResource;

class MemberManagementController extends AppBaseController
{   
    private $memberManagementRepository;
    public function __construct(MemberManagementRepository $memberManagementRepository)
    {
        $this->memberManagementRepository = $memberManagementRepository;
    }
    
    public function index($component,$slug)
    {   
        try {
            $member_mangement = $this->memberManagementRepository->index($component,$slug);
            if($member_mangement){
                  return $this->sendResponse(MemberManagementResource::collection($member_mangement), __('responses.member_manager_found'));
            }
            return $this->sendError(__('responses.member_manager_not_found'));
        }catch (\Exception $e){
            
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteMultiple($component,$slug,Request $request)
    {   
        try {
            $member_mangement=$this->memberManagementRepository->deleteMultiple($component,$slug,$request);
            if($member_mangement){
                return $this->sendResponse(null, __('responses.member_manager_delete'));
               }
               return $this->sendError(__('responses.member_manager_not_delete'));
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create($component,$slug,Request $request)
    {
        try {
            $member_mangement=$this->memberManagementRepository->create($component,$slug,$request);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
