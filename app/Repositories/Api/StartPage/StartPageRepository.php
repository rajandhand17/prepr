<?php

namespace App\Repositories\Api\StartPage;

use App\Services\FeaturedLabService;
use App\Services\FeaturedSkillService;
use App\Services\PartnerCompaniesService;
use App\Services\Public\LabService;
use App\Services\SkillService;
use App\Services\TestimonialsService;
use App\Services\UserService;

class StartPageRepository implements StartPageInterface
{
    private $featuredLabService;

    private $labService;

    private $featuredSkillService;

    private $skillService;

    private $partnerCompaniesService;

    private $testimonialsService;

    private $userService;

    public function __construct(UserService $userService, TestimonialsService $testimonialsService, PartnerCompaniesService $partnerCompaniesService,SkillService $skillService, FeaturedLabService $featuredLabService,LabService $labService,FeaturedSkillService $featuredSkillService)
    {
        $this->featuredLabService =$featuredLabService;
        $this->labService =$labService;
        $this->featuredSkillService =$featuredSkillService;
        $this->skillService=$skillService;
        $this->partnerCompaniesService=$partnerCompaniesService;
        $this->testimonialsService=$testimonialsService;
        $this->userService=$userService;
    }

    public function index()
    {
        try {
            $getLabsData=[];
            $getSkillsData=[];
            $companies=[];
            $testimonials=[];
            $labIds =$this->featuredLabService->getFeaturedLab()->pluck('lab_id');
            if($labIds){
                $getLabsData=$this->labService->getLabsBasedOnIds($labIds);
            }
            $skillIds=$this->featuredSkillService->getFeaturedSKill()->pluck('skill_id');
            if($skillIds){
                $getSkillsData=$this->skillService->getSkillBasedOnIds($skillIds);
            }

            $partnerCompanies=$this->partnerCompaniesService->getPartnerCompanies();
            if($partnerCompanies){
                $companies=$partnerCompanies;
            }
            $testimonial=$this->testimonialsService->getUsers();
            if($testimonial){
                $testimonials=$testimonial;
            }
            $data=[
                'labs'=>$getLabsData,
                'skills'=>$getSkillsData,
                'partners'=>$companies,
                'testimonials'=>$testimonials,
            ];
            if($data){
                return $data;
            }

            return false;
        }catch (\Exception $e) {
            dd($e);
            return false;
        }
    }
}
