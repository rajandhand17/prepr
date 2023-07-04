<?php

namespace App\Repositories\Api\Lab;

use App\Http\Requests\Lab\CheckLabSlug;
use App\Services\LabExternalLinksService;
use App\Services\LabService;
use App\Services\MemberManagementService;
use App\Services\LabAddressService;
use App\Services\LabSkillsGroupsStackService;
use App\Services\LabTagsGroupsService;
use App\Services\LabAcheivementService;
use App\Services\LabChallengesService;
use App\Services\SkillService;
use App\Services\FavoriteService;
class LabRepository implements LabInterface
{
    private $labService;
    private $memberManagementService;
    private $labAddressService;
    private $labExternalLinksService;
    private $labSkillsGroupsStackService;
    private $labTagsGroupsService;
    private $labAcheivementService;
    private $labChallengesService;
    private $skillService;
    private $favoriteService;

    public function __construct(LabService $labService, MemberManagementService $memberManagementService,LabAddressService $labAddressService,LabExternalLinksService $labExternalLinksService,LabSkillsGroupsStackService $labSkillsGroupsStackService,LabTagsGroupsService $labTagsGroupsService,LabAcheivementService $labAcheivementService,LabChallengesService $labChallengesService,SkillService $skillService,FavoriteService $favoriteService)
    {   
        $this->labService = $labService;
        $this->memberManagementService=$memberManagementService;
        $this->labAddressService=$labAddressService;
        $this->labExternalLinksService=$labExternalLinksService;
        $this->labSkillsGroupsStackService=$labSkillsGroupsStackService;
        $this->labTagsGroupsService=$labTagsGroupsService;
        $this->labAcheivementService=$labAcheivementService;
        $this->labChallengesService=$labChallengesService;
        $this->skillService=$skillService;
        $this->favoriteService=$favoriteService;
    }

    public function uploadCoverImage($image){
        try {
            return $this->labService->uploadCoverImage($image);
        } catch (\Exception $e){
            return false;
        }
    }
    public function store($component,$request,$upload_profile_image,$upload_acheivements_image)
    {
        try {
            $addLab=$this->labService->store($request,$upload_profile_image);
            if($addLab!==false){
                $labAddress=$this->labAddressService->store($request,$addLab);
                $labSkillsGroupsStack=$this->labSkillsGroupsStackService->store($request,$addLab);
                $labTagsGroupsService=$this->labTagsGroupsService->store($request,$addLab);
                if (!empty($request->link_url) && !empty($request->social_name)){
                    $labExternalLinks=$this->labExternalLinksService->store($request,$addLab);
                }
                if($request->is_achievement_enabled=="yes"){
                    $labAcheivementService=$this->labAcheivementService->store($request,$addLab,$upload_acheivements_image);
                } 
                if($request->is_associated_challenge=="yes"){  
                    $storeChallengeIdService=$this->labChallengesService->storeChallengeId($request,$addLab);
                }
                if($request->is_associated_resource=="yes"){ 
                    $storeChallengePathId=$this->labChallengesService->storeChallengePathId($request,$addLab);
                }
                $memberList = [];
            if ($request->invite_type == 'csv') {
                $memberList = $this->memberManagementService->getRecordsFromCsv($request);
                if (!$memberList && !count($memberList) > 0) {
                    return false;
                }
            }
            if ($request->invite_type == 'email') {
                $memberList = $this->memberManagementService->getRecordsFromEmailArray($request);
                if (!$memberList && !count($memberList) > 0) {
                    return false;
                }
            }
            if (is_array($memberList) && count($memberList) > 0) {
                $checkStatus = $this->memberManagementService->addMembers($addLab, $component, $request, $memberList);
                if ($checkStatus != false) {
                    return true;
                }
                return false;
            }
            }
        
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabList($request){
        try {
            $lab = $this->labService->getLabList($request);
            if ($lab) {
                return $lab;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabDetailed($slug){
        try {
            $labDetailed=$this->labService->getLabDetailed($slug);
            return $labDetailed;
        } catch (\Exception $e){
        return false;
        }
    }
    
    public function checkLabSlug($slug){
        try {
            $labSlug=$this->labService->checkLabSlug($slug);
            return $labSlug;
        } catch (\Exception $e){
            return false;
        }
    }

    public function checkLabNameExistsOrNot($name){
        try {
            $labSlug=$this->labService->checkLabNameExistsOrNot($name);
            return $labSlug;
        } catch (\Exception $e){
            return false;
        }
    }

    public function getSkills($request){
        try {
            $getSkills=$this->skillService->getSkillLists($request);
            if($getSkills){
                return $getSkills;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function likeUnlike($request){
        try {
            $likeUnlikeLab=$this->favoriteService->create($request);
            if($likeUnlikeLab){
                return true;
            }
            return false;
        } catch (\Exception $e){
            return false;
        }
    }

    public function isAlreadyLikedOrNotLiked($request){
        try {
            $isAlreadyLikeOrNotLike=$this->favoriteService->isAlreadyLikedOrNotLiked($request);
            if($isAlreadyLikeOrNotLike){
                return true;
            }
            return false;
        } catch (\Exception $e){
         return false;
        }
    }
    

}
