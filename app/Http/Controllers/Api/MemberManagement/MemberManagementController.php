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
    
    /**
     * @OA\Get(
     *     path="/api/v1/member-management/{component}/{slug}?language=en",
     *     tags={"Member Management API -  List"},
     *     summary="Finds lists of Member Management",
     *     description="Get all the Member Management",
     *     security={{"bearerAuth":{}}},
     *     operationId="index",
     *     @OA\Parameter(
     *         name="language",
     *         in="path",
     *         required=true,
     *         description="language define the choosen language",
     *        
     *     ),
     *     @OA\Parameter(
     *         name="component",
     *         in="path",
     *         required=true,
     *         description="component define type",
     *        
     *     ),
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="slug define the organization slug",
     *        
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error!",
     *
     *     ),
     * )
     */
    public function index($component,$slug,Request $request)
    {     
        try {
            $member_mangement = $this->memberManagementRepository->index($component,$slug,$request);
            if($member_mangement){
              return $this->sendResponse(MemberManagementResource::collection($member_mangement), __('responses.member_manager_found'));
            }
            return $this->sendError(__('responses.member_manager_not_found'),404);
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
    
    public function create($component,$slug,CreateMemberManagementRequest $request)
    {  
        try {
            $member_mangement=$this->memberManagementRepository->create($component,$slug,$request);
            if($member_mangement){
                return $member_mangement;
            }
            return $this->sendError(__('responses.create_member_manger_failed'),201);
        }catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
    
    public function delete($component,$slug,DeleteMemberManagementRequest $request)
    {   
        try {
            $member_mangement=$this->memberManagementRepository->delete($component,$slug,$request);
            if($member_mangement){
                return $this->sendResponse(null, __('responses.member_manager_delete'));
               }
               return $this->sendError(__('responses.member_manager_not_delete'),400);
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

}
