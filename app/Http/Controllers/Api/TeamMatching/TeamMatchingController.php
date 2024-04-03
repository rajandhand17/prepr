<?php

namespace App\Http\Controllers\Api\TeamMatching;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Manage\LabProgram\LabProgramResource;
use App\Http\Resources\TeamMatching\PendingRequestsResource;
use App\Http\Resources\TeamMatching\TeamMatchingResource;
use App\Repositories\Api\TeamMatching\TeamMatchingRepository;
use App\Services\ProjectMemberManagementService;
use App\Services\ProjectService;
use App\Services\UserService;
use App\Services\UserSkillsService;
use Illuminate\Http\Request;

class TeamMatchingController extends AppBaseController
{
    private $teamMatchingRepository;
    public function __construct(TeamMatchingRepository $teamMatchingRepository){
        $this->teamMatchingRepository=$teamMatchingRepository;
    }

    public function index(){
        try {
            $getUsersSkills=UserSkillsService::getUserSkills();
            if($getUsersSkills){
                $getProjectListing=$this->teamMatchingRepository->getProjectListingBasedOnSkills($getUsersSkills);
                if($getProjectListing){
                    return $this->sendResponse(TeamMatchingResource::collection($getProjectListing),__('responses.team_matching_list_successfully'));
                }
                return $this->sendResponse([],__('responses.team_matching_list_successfully'));
            }
            return $this->sendError(__('responses.skills_list_failed'), 403);
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function pendingRequests($action,Request $request){
        try {
            if (!in_array($action, ['pending', 'matched'])) {
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            switch ($action) {
                case 'pending':
                     $getPendingRequests=$this->teamMatchingRepository->getPendingRequests($request);
                     if(!empty($getPendingRequests)){
                     $response= [
                                    'total_count'  => $getPendingRequests->total(),
                                    'per_page'     => $getPendingRequests->perPage(),
                                    'count'        => $getPendingRequests->count(),
                                    'current_page' => $getPendingRequests->currentPage(),
                                    'total_pages'  => $getPendingRequests->lastPage(),
                                    'list'         => PendingRequestsResource::collection($getPendingRequests),
                                ];
                     $message=__('responses.team_matching_list_successfully');
                    }else{
                         $response=[];
                         $message=__('responses.team_matching_list_successfully');
                     }
                    break;
                case 'matched':
                    $getMatchingRequest=$this->teamMatchingRepository->getMatchingTeams();
                    if(!empty($getMatchingRequest)){
                        $response= [
                            'total_count'  => $getMatchingRequest->total(),
                            'per_page'     => $getMatchingRequest->perPage(),
                            'count'        => $getMatchingRequest->count(),
                            'current_page' => $getMatchingRequest->currentPage(),
                            'total_pages'  => $getMatchingRequest->lastPage(),
                            'list'         => TeamMatchingResource::collection($getMatchingRequest),
                        ];
                        $message=__('responses.team_matching_list_successfully');
                    }else{
                        $response=[];
                        $message=__('responses.team_matching_list_successfully');
                    }
                    break;
            }
            return $this->sendResponse($response, __('responses.team_matching_list_successfully'));

        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

}
