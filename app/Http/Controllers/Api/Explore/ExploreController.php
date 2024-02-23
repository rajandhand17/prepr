<?php

namespace App\Http\Controllers\Api\Explore;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Explore\LabResource;
use App\Http\Resources\Explore\TagResource;
use App\Http\Resources\Explore\SkillResource;
use App\Repositories\Api\Explore\ExploreRepository;
use Illuminate\Http\Request;

class ExploreController extends AppBaseController
{
    private $exploreRepository;
    public function __construct(ExploreRepository $exploreRepository){
        $this->exploreRepository=$exploreRepository;
    }

    public function index($action,Request $request){
        try {
            if($action=='recommended'){
                $explore = $this->exploreRepository->recommended($request);
            }else{
                $explore = $this->exploreRepository->index($request);
            }
            if($explore){
                if($action=='recommended'){
                    $response=[
                        'trending_labs'  =>LabResource::collection($explore['trending_labs']),
                        'recommended_labs'  =>LabResource::collection($explore['recommended_labs']),

                    ];
                }else{

                }

                return $this->sendResponse($response, __('responses.found_user_profile_detail'));
            }
            return $this->sendError(__('responses.send_error'),404);
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);

        }
    }

    public function recommended(Request $request){
        try {
            $explore = $this->exploreRepository->recommended($request);
            if($explore){
               $response=[
                    'trending_labs'  =>LabResource::collection($explore['trending_labs']),
                    'recommended_labs'  =>LabResource::collection($explore['recommended_labs']),

                ];
                return $this->sendResponse($response, __('responses.found_user_profile_detail'));
            }
            return $this->sendError(__('responses.send_error'),404);
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
