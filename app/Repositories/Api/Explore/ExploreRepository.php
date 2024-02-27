<?php

namespace App\Repositories\Api\Explore;


use App\Services\LabTagGroupService;
use App\Services\Public\LabService;
use App\Services\SkillService;
use App\Services\TagService;

class ExploreRepository implements ExploreInterface
{
    private $labService;

    private $skillService;

    private $tagService;

    private $labTagGroupService;

    public function __construct(LabService $labService, SkillService $skillService,TagService $tagService, LabTagGroupService $labTagGroupService)
    {
        $this->labService =$labService;
        $this->skillService=$skillService;
        $this->tagService=$tagService;
        $this->labTagGroupService=$labTagGroupService;
    }

    public function index($request){
        try {
            $explore=[];
            $explore['labs']=$this->labService->getTrendingLab($request);
            $explore['skills']=$this->skillService->getTrendingSkillsList();
            $explore['tags']=$this->tagService->getTrendingTags();
            return $explore;
        }catch (\Exception $e) {
            return false;
        }
    }

    public function recommended($request)
    {
        try {
            $recommendedData=[];
            $recommendedData['trending_labs']=$this->labService->getTrendingLab($request);
            $recommendedData['recommended_labs']=$this->labService->recommendedLab($request);
            return $recommendedData;
        }catch (\Exception $e){
            return false;
        }
    }

    public function trendingTopics(){
        try{
         return  $this->labTagGroupService->getTrendingTopics();
        }catch (\Exception $e){
            return false;
        }
    }
}
