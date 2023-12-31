<?php

namespace App\Repositories\Api\Manage\LabMarketplace;

use App\Services\Manage\LabAddressMarketplaceService;
use App\Services\Manage\LabMarketplaceAchievementsService;
use App\Services\Manage\LabMarketplaceExternalLinksService;
use App\Services\Manage\LabMarketplaceSkillsGroupStackService;
use App\Services\Manage\LabMarketplaceTagsGroupService;
use App\Services\Manage\LabService;
use App\Services\Manage\LabTemplateAchievementsService;
use App\Services\Manage\LabTemplateAddressService;
use App\Services\Manage\LabTemplateComponentAssociationService;
use App\Services\Manage\LabTemplateExternalLinksService;
use App\Services\Manage\LabTemplateService;
use App\Services\Manage\LabTemplateSkillsGroupsStackService;
use App\Services\Manage\LabTemplateTagsGroupsService;
use App\Services\Manage\LabMarketplaceService;
use DB;

class LabMarketplaceRepository implements LabMarketplaceInterface
{
    private $labMarketplaceService;

    private $labService;

    private $labAddressMarketplaceService;

    private $labMarketplaceSkillsGroupStackService;

    private $labMarketplaceTagsGroupsService;

    private $labMarketplaceExternalLinksService;

    private $labMarketplaceAchievementsService;

    private $labMarketplaceComponentAssociationService;
    public function __construct(LabMarketplaceAchievementsService $labMarketplaceAchievementsService, LabMarketplaceExternalLinksService $labMarketplaceExternalLinksService, LabMarketplaceTagsGroupService $labMarketplaceTagsGroupsService, LabMarketplaceSkillsGroupStackService $labMarketplaceSkillsGroupStackService, LabMarketplaceService $labMarketplaceService, LabService $labService, LabAddressMarketplaceService $labAddressMarketplaceService){
        $this->labMarketplaceService = $labMarketplaceService;
        $this->labService = $labService;
        $this->labAddressMarketplaceService=$labAddressMarketplaceService;
        $this->labMarketplaceSkillsGroupStackService=$labMarketplaceSkillsGroupStackService;
        $this->labMarketplaceTagsGroupsService=$labMarketplaceTagsGroupsService;
        $this->labMarketplaceExternalLinksService=$labMarketplaceExternalLinksService;
        $this->labMarketplaceAchievementsService=$labMarketplaceAchievementsService;
    }
    public function getLabBasedOnSlug($slug){
        try {
            return $this->labService->getLabBasedOnSlug($slug);
        }catch (\Exception $e) {
            return false;
        }
    }

    public function createLabMarketplace($slug,$lab){
        try {
            $createLabMarketplace= DB::transaction(function () use ($slug,$lab){
                $createLabMarketplace=$this->labMarketplaceService->createLabMarketplace($slug);
                $createLabAddressMarketplace=$this->labAddressMarketplaceService->createLabAddressMarketplace($createLabMarketplace,$lab);
                $createdLabSkillAssociations = $this->labMarketplaceSkillsGroupStackService->createLabMarketplaceSkillsGroupsStack($createLabMarketplace, $lab);
           //     $createdLabTemplateTagAssociations = $this->labMarketplaceTagsGroupsService->createLabMarketplaceSkillsGroupsStack($createLabMarketplace, $lab);
                $createdLabTemplateExternalLinks = $this->labMarketplaceExternalLinksService->createLabMarketplaceExternalLinks($createLabMarketplace, $lab);
                $createdLabTemplateAchievement = $this->labMarketplaceAchievementsService->createLabMarketplaceAchievements($createLabMarketplace, $lab);
                $createdLabTemplateAssociations = $this->labTemplateComponentAssociationService->createLabTemplateAssociation($createLabMarketplace, $lab);
                return[
                    "labMarketplace" => $createLabMarketplace,
                ];
            });
            if($createLabMarketplace['labMarketplace']){
                return $createLabMarketplace['labMarketplace'];
            }
                return $this->labMarketplaceService->createLabMarketplace($slug);
        }catch (\Exception $e) {
            return false;
        }
    }
}
