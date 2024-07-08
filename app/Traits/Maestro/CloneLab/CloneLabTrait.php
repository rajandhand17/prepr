<?php

namespace App\Traits\Maestro\CloneLab;

use App\Services\Maestro\Lab\LabService;
use App\Services\Maestro\LabMarketplaceService;
use Exception;
use Illuminate\Support\Facades\DB;

trait CloneLabTrait
{
    protected $labService;
    protected $organizationService;
    protected $languageService;
    protected $labAddressService;
    protected $labSkillsGroupsStackService;
    protected $labTagsGroupsService;
    protected $labExternalLinksService;
    protected $labAchievementService;

    public function getOrganization()
    {
        try {
            $organization=$this->organizationService->getOrganizations();
            if($organization){
                return $organization;
            }
            return false;
        }catch(Exception $e){
            return false;
        }
    }

    public function getAllLabs()
    {
        try {
            $labService = $this->labService->getList();
            if($labService){
                return $labService;
            }
            return false;
        }catch(Exception $e){
            return false;
        }
    }

    public function getAllLanguages()
    {
        try {
            $languages=$this->languageService->getLanguages();
            if($languages){
                return $languages;
            }
            return false;
        }catch(Exception $e){
            return false;
        }
    }

    public function createLab($request)
    {
        try {
            $lab=LabService::getLabById($request->lab);
            $createdLab = DB::transaction(function () use ($lab,$request) {
                $newLab=$this->labService->createLab($lab,$request->organization);
                $labAddress=$this->labAddressService->createLabAddress($lab,$newLab);
                $labSKillsGroupStack=$this->labSkillsGroupsStackService->createLabSkillsGroupsStack($lab,$newLab);
                $labTagGroupStack=$this->labTagsGroupsService->createLabTagsGroups($lab,$newLab);
                $labExternalLinks = $this->labExternalLinksService->createLabExternalLinks($lab,$newLab);
                $createdLabAchievement = $this->labAchievementService->createLabAchievement($lab,$newLab);
                return [
                    'lab'                    =>$newLab,
                    'lab_address'            =>$labAddress,
                    'lab_sKills_group_stack' =>$labSKillsGroupStack,
                    'lab_tag_group_stack'    =>$labTagGroupStack,
                    'lab_external_links'     =>$labExternalLinks,
                    'lab_achievement'        =>$createdLabAchievement,
                ];
            });
            if($createdLab['lab'] && $createdLab['lab_address'] && $createdLab['lab_sKills_group_stack']
                && $createdLab['lab_tag_group_stack'] && $createdLab['lab_external_links'] && $createdLab['lab_achievement']){
                DB::commit();
                return $createdLab['lab'];
            }
            DB::rollBack();

            return false;
        }catch(Exception $e){
            DB::rollback();
            return false;
        }
    }
}
