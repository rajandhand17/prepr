<?php

namespace App\Http\Controllers\Api\Explore;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Explore\ChallengeResource;
use App\Http\Resources\Explore\FeaturedResource;
use App\Http\Resources\Explore\LabResource;
use App\Http\Resources\Explore\SkillResource;
use App\Repositories\Api\Explore\ExploreRepository;
use Illuminate\Http\Request;

class ExploreController extends AppBaseController
{
    private $exploreRepository;
    public function __construct(ExploreRepository $exploreRepository){
        $this->exploreRepository=$exploreRepository;
    }
    public function index($action=null){
        try {
            $response=[];
            switch ($action){
                case 'recommended':
                    $explore = $this->exploreRepository->recommended();
                    if($explore){
                        $response=[
                            'labs'       =>LabResource::collection($explore['labs']),
                            'challenge'  =>ChallengeResource::collection($explore['challenge']),
                        ];
                        $message=__('responses.recommended_labs_challenges_successfully');
                    }
                    $response=__('responses.recommended_labs_challenges_failed');
                    break;
                case 'featured':
                    $featured = $this->exploreRepository->getFeaturedLabs();
                    if($featured){
                        $response=FeaturedResource::collection($featured);
                        $message=__('responses.featured_labs_successfully');
                    }
                    $message=__('responses.featured_labs_failed');
                    break;
                default:
                    return $this->sendError(__('responses.handler_bad_request'), 400);
                    break;
            }
            if($response){
                return $this->sendResponse($response, $message);
            }
            return $this->sendError(__('responses.send_error'),404);
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }


    public function recommendedSkills(){
        try {
            $recommendedSkills = $this->exploreRepository->recommendedSkills();
            if($recommendedSkills){
                  return $this->sendResponse(SkillResource::collection($recommendedSkills),__('responses.recommended_skills_successfully'));
            }
            return $this->sendResponse([],__('responses.recommended_skills_successfully'));
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

}
