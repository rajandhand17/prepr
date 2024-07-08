<?php

namespace App\Traits\Maestro\CloneLab;

use App\Models\LabSkillsGroupsStack;
use App\Services\DurationService;
use App\Services\Maestro\Challenge\ChallengeService;
use App\Services\Maestro\Lab\LabService;
use App\Services\Maestro\LabAchievement\LabAchievementService;
use App\Services\Maestro\LabExternalLinks\LabExternalLinksService;
use App\Services\Maestro\LabSkillsGroupsStack\LabSkillsGroupsStackService;
use App\Services\Maestro\LabTagsGroups\LabTagsGroupsService;
use App\Services\Maestro\Language\LanguageService;
use App\Services\Maestro\Organization\organizationservice;
use App\Services\Maestro\LabMarketplaceService;
use App\Services\Maestro\LabAddress\LabAddressService;
use Exception;
use Illuminate\Support\Facades\DB;

trait CloneLabTrait
{
    private $labService;
    public function __construct(LabService $labService)
    {
        $this->labService = $labService;
    }
    public function getOrganization()
    {
        try {
            $organization=organizationservice::getOrganizations();
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
            $labService=LabService::getList();
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
            $languages=LanguageService::getLanguages();
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
                $newLab=LabService::createLab($lab,$request->organization);
                $labAddress=LabAddressService::createLabAddress($lab,$newLab);
                $labSKillsGroupStack=LabSkillsGroupsStackService::createLabSkillsGroupsStack($lab,$newLab);
                $labTagGroupStack=LabTagsGroupsService::createLabTagsGroups($lab,$newLab);
                $labExternalLinks = LabExternalLinksService::createLabExternalLinks($lab,$newLab);
                $createdLabAchievement = LabAchievementService::createLabAchievement($lab,$newLab);
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
