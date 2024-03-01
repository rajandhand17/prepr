<?php

namespace App\Http\Controllers\Api\Explore;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Explore\ChallengeResource;
use App\Http\Resources\Explore\ExploreResource;
use App\Http\Resources\Explore\LabResource;
use App\Http\Resources\Explore\TagResource;
use App\Http\Resources\Explore\SkillResource;
use App\Http\Resources\Explore\TrendingTopicsResource;
use App\Http\Resources\Explore\UserResource;
use App\Repositories\Api\Explore\ExploreRepository;
use Illuminate\Http\Request;

class ExploreController extends AppBaseController
{
    private $exploreRepository;
    public function __construct(ExploreRepository $exploreRepository){
        $this->exploreRepository=$exploreRepository;
    }
    public function index(Request $request,$action=null){
        try {
            $response=[];
            switch ($action){
                case 'recommended':
                    $explore = $this->exploreRepository->recommended($request);
                    if($explore){
                        $response=[
                            'labs'       =>LabResource::collection($explore['labs']),
                            'challenge'  =>ChallengeResource::collection($explore['challenge']),
                        ];
                    }
                    break;
                case 'featured':
                    $featured = $this->exploreRepository->getFeaturedLabs();
                    if($featured){
                        $response=LabResource::collection($featured);
                    }
                    break;
                default:
                    $explore = $this->exploreRepository->index();
                    if($explore){
                        $response=LabResource::collection($explore);
                    }
                    break;
            }
            if($response){
                return $this->sendResponse($response, __('responses.found_user_profile_detail'));
            }
            return $this->sendError(__('responses.send_error'),404);
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function recommendedSkill(){
        try{
            $skilled=$this->exploreRepository->recommendedSkill();
            if($skilled){
                return $this->sendResponse($skilled,__('responses.recommended'));
            }
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
