<?php

namespace App\Http\Controllers\Api\Explore;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Explore\LabResource;
use App\Http\Resources\Explore\TagResource;
use App\Http\Resources\Explore\SkillResource;
use App\Http\Resources\Explore\TrendingTopicsResource;
use App\Repositories\Api\Explore\ExploreRepository;
use Illuminate\Http\Request;

class ExploreController extends AppBaseController
{
    private $exploreRepository;
    public function __construct(ExploreRepository $exploreRepository){
        $this->exploreRepository=$exploreRepository;
    }

    public function recommended(Request $request){
        try {
            $recommended = $this->exploreRepository->recommended($request);
            if($recommended){
                $response = [
                    'total_count'  => $recommended->total(),
                    'per_page'     => $recommended->perPage(),
                    'count'        => $recommended->count(),
                    'current_page' => $recommended->currentPage(),
                    'total_pages'  => $recommended->lastPage(),
                    'list'         => $recommended,
                ];
                return $this->sendResponse($response, __('responses.recommended'));
            }
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
    public function index(Request $request,$action=null){
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
                    $response=[
                        'labs'  =>LabResource::collection($explore['labs']),
                        'skills'=>SkillResource::collection($explore['skills']),
                        'tags'  =>TagResource::collection($explore['tags']),
                    ];
                }
                return $this->sendResponse($response, __('responses.found_user_profile_detail'));
            }
            return $this->sendError(__('responses.send_error'),404);
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function trendingTopics(){
        try {
            $explore=$this->exploreRepository->trendingTopics();
            if($explore){
                return $this->sendResponse(TrendingTopicsResource::collection($explore),__('responses.trending_topics_successfully'));
            }
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
