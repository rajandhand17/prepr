<?php

namespace App\Repositories\Api\Manage\LabMarketplace;

use App\Services\Manage\LabMarketplaceAddressService;
use App\Services\Manage\LabMarketplaceAchievementsService;
use App\Services\Manage\LabMarketplaceComponentAssociationService;
use App\Services\Manage\LabMarketplaceExternalLinksService;
use App\Services\Manage\LabMarketplaceSkillsGroupStackService;
use App\Services\Manage\LabMarketplaceTagsGroupService;
use App\Services\Manage\LabService;
use App\Services\Manage\LabMarketplaceService;
use DB;

class LabMarketplaceRepository implements LabMarketplaceInterface
{
    private $labMarketplaceService;

    private $labService;

    private $labMarketplaceAddressService;

    private $labMarketplaceSkillsGroupStackService;

    private $labMarketplaceTagsGroupsService;

    private $labMarketplaceExternalLinksService;

    private $labMarketplaceAchievementsService;

    private $labMarketplaceComponentAssociationService;
    public function __construct(LabMarketplaceComponentAssociationService $labMarketplaceComponentAssociationService, LabMarketplaceAchievementsService $labMarketplaceAchievementsService, LabMarketplaceExternalLinksService $labMarketplaceExternalLinksService, LabMarketplaceTagsGroupService $labMarketplaceTagsGroupsService, LabMarketplaceSkillsGroupStackService $labMarketplaceSkillsGroupStackService, LabMarketplaceService $labMarketplaceService, LabService $labService, LabMarketplaceAddressService $labMarketplaceAddressService){
        $this->labMarketplaceService = $labMarketplaceService;
        $this->labService = $labService;
        $this->labMarketplaceAddressService=$labMarketplaceAddressService;
        $this->labMarketplaceSkillsGroupStackService=$labMarketplaceSkillsGroupStackService;
        $this->labMarketplaceTagsGroupsService=$labMarketplaceTagsGroupsService;
        $this->labMarketplaceExternalLinksService=$labMarketplaceExternalLinksService;
        $this->labMarketplaceAchievementsService=$labMarketplaceAchievementsService;
        $this->labMarketplaceComponentAssociationService=$labMarketplaceComponentAssociationService;
    }
    public function getLabBasedOnSlug($slug){
        try {
            return $this->labService->getLabBasedOnSlug($slug);
        }catch (\Exception $e) {
            return false;
        }
    }
    public function getCheckUuid($uuid){
        try {
            return $this->labMarketplaceService->getCheckUuid($uuid);
        }catch (\Exception $e){
            return false;
        }
    }
    public function createLabMarketplace($slug,$labId,$organizationId){
        try {
            $createLabMarketplace= DB::transaction(function () use ($slug,$labId,$organizationId){
                $createLabMarketplace=$this->labMarketplaceService->createLabMarketplace($slug,$organizationId);
                $createLabMarketplaceAddress=$this->labMarketplaceAddressService->createLabMarketplaceAddress($createLabMarketplace,$labId);
                $createdLabMarketplaceSkillAssociations = $this->labMarketplaceSkillsGroupStackService->createLabMarketplaceSkillsGroupsStack($createLabMarketplace, $lab);
                $createdLabMarketplaceTagAssociations = $this->labMarketplaceTagsGroupsService->createLabMarketplaceTagsGroupsStack($createLabMarketplace, $lab);
                $createdLabMarketplaceExternalLinks = $this->labMarketplaceExternalLinksService->createLabMarketplaceExternalLinks($createLabMarketplace, $lab);
                $createdLabMarketplaceAchievement = $this->labMarketplaceAchievementsService->createLabMarketplaceAchievements($createLabMarketplace, $lab);
                $createdLabMarketplaceAssociations = $this->labMarketplaceComponentAssociationService->createMarketplaceComponentAssociation($createLabMarketplace, $lab);
                $updateLab=$this->labService->updatePreBuilt($lab->id,'1');
                return[
                    "labMarketplace" => $createLabMarketplace,
                    'createLabMarketplaceAddress'=>$createLabMarketplaceAddress,
                    'createdLabMarketplaceSkillAssociations'=>$createdLabMarketplaceSkillAssociations,
                    'createdLabMarketplaceTagAssociations'=>$createdLabMarketplaceTagAssociations,
                    'createdLabMarketplaceExternalLinks'=>$createdLabMarketplaceExternalLinks,
                    'createdLabMarketplaceAchievement'=>$createdLabMarketplaceAchievement,
                    'createdLabMarketplaceAssociations'=>$createdLabMarketplaceAssociations,
                    'updateLab'=>$updateLab,
                ];
            });
            if($createLabMarketplace['createdLabMarketplaceAssociations'] && $createLabMarketplace['createdLabMarketplaceAchievement'] && $createLabMarketplace['createdLabMarketplaceExternalLinks'] && $createLabMarketplace['createdLabMarketplaceTagAssociations'] && $createLabMarketplace['createdLabMarketplaceSkillAssociations'] && $createLabMarketplace['labMarketplace'] && $createLabMarketplace['createLabMarketplaceAddress'] && $createLabMarketplace['updateLab']){
                DB::commit();
                return $createLabMarketplace['labMarketplace'];
            }
            DB::rollback();
            return false;
        }catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
}
