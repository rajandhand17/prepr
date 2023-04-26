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
    
     /**
     * @OA\Post(
     *     path="/api/v1/member-management/organization/prepr/create?language=en",
     *     tags={"Member Management API -  create"},
     *     summary="Send request for create member management",
     *     operationId="creates",
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Enter type for member management!",
     *         required=true,
     *         explode=true,
     *     ),
     * 
     *     @OA\Parameter(
     *         name="invite_type",
     *         in="query",
     *         description="Enter invite-type for member management!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="Enter role for member management!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="module_id",
     *         in="query",
     *         description="Enter module_id for member management!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="inviter_id",
     *         in="query",
     *         description="Enter inviter id for member management!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="subject_line",
     *         in="query",
     *         description="Enter subject line for member management!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="email_body",
     *         in="query",
     *         description="Enter email body for member management!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="invite_status",
     *         in="query",
     *         description="Enter invite status for member management!",
     *         required=true,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="invite_email",
     *         in="query",
     *         description="Enter invite email for member management!",
     *         required=true,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="inviter_id",
     *         in="query",
     *         description="Enter inviter id for member management!",
     *         required=true,
     *         explode=true,
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
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    
    public function create($component,$slug,CreateMemberManagementRequest $request)
    {  
        try {
            if(!in_array($request->invite_type,["csv","email","network"])){
                return $this->sendError(__('responses.member_manage_type'),400);
            }
            $member_mangement=$this->memberManagementRepository->create($component,$slug,$request);
            if($member_mangement){
                return $this->sendResponse(null, __('responses.create_member_manger_success'));
            }
            return $this->sendError(__('responses.create_member_manger_failed'),403);
        }catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
 
     /**
     * @OA\Post(
     *     path="/api/v1/member-management/organization/prepr/delete?language=en",
     *     tags={"Member Management API - delete"},
     *     summary="Member management apis delete",
     *     operationId="deletes",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Enter inviter id for member management!",
     *         required=true,
     *         explode=true,
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
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */   
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
