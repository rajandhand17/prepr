<?php

namespace App\Repositories\Api\Manage\LabTemplate;

use App\Services\Manage\LabTemplateAchievementsService;
use App\Services\Manage\LabTemplateAddressService;
use App\Services\Manage\LabTemplateComponentAssociationService;
use App\Services\Manage\LabTemplateExternalLinksService;
use App\Services\Manage\LabTemplateService;
use App\Services\Manage\LabTemplateSkillsGroupsStackService;
use App\Services\Manage\LabTemplateTagsGroupsService;
use DB;

class LabTemplateRepository implements LabTemplateInterface
{
    private $labTemplateService;
    private $labTemplateAddressService;
    private $labTemplateSkillsGroupStackService;
    private $labTemplateTagsGroupsService;
    private $labTemplateExternalLinksService;
    private $labTemplateAchievementsService;

    private $labTemplateComponentAssociationService;

    public function __construct(LabTemplateService $labTemplateService, LabTemplateAddressService $labTemplateAddressService, LabTemplateSkillsGroupsStackService $labTemplateSkillsGroupStackService, LabTemplateTagsGroupsService $labTemplateTagsGroupsService, LabTemplateExternalLinksService $labTemplateExternalLinksService, LabTemplateAchievementsService $labTemplateAchievementsService, LabTemplateComponentAssociationService $labTemplateComponentAssociationService)
    {
        $this->labTemplateService = $labTemplateService;
        $this->labTemplateAddressService = $labTemplateAddressService;
        $this->labTemplateSkillsGroupStackService = $labTemplateSkillsGroupStackService;
        $this->labTemplateTagsGroupsService = $labTemplateTagsGroupsService;
        $this->labTemplateExternalLinksService = $labTemplateExternalLinksService;
        $this->labTemplateAchievementsService = $labTemplateAchievementsService;
        $this->labTemplateComponentAssociationService = $labTemplateComponentAssociationService;
    }

    public function createLabTemplate($slug, $lab)
    {
        try {
            $createdTemplateLab = DB::transaction(function () use ($slug, $lab) {
                $createLabTemplate = $this->labTemplateService->createLabTemplate($slug);
                $createdLabTemplateAddress = $this->labTemplateAddressService->createLabTemplateAddress($createLabTemplate, $lab);
                $createdLabTemplateSkillAssociations = $this->labTemplateSkillsGroupStackService->createLabTemplateSkillsGroupsStack($createLabTemplate, $lab);
                $createdLabTemplateTagAssociations = $this->labTemplateTagsGroupsService->createLabTemplateSkillsGroupsStack($createLabTemplate, $lab);
                $createdLabTemplateExternalLinks = $this->labTemplateExternalLinksService->createLabTemplateExternalLinks($createLabTemplate, $lab);
                $createdLabTemplateAchievement = $this->labTemplateAchievementsService->createLabTemplateAchievement($createLabTemplate, $lab);
                $createdLabTemplateAssociations = $this->labTemplateComponentAssociationService->createLabTemplateAssociation($createLabTemplate, $lab);

                return [
                    'createdLabTemplate'                  => $createLabTemplate,
                    'createdLabTemplateAddress'           => $createdLabTemplateAddress,
                    'createdLabTemplateSkillAssociations' => $createdLabTemplateSkillAssociations,
                    'createdLabTemplateTagAssociations'   => $createdLabTemplateTagAssociations,
                    'createdLabTemplateExternalLinks'     => $createdLabTemplateExternalLinks,
                    'createdLabTemplateAchievement'       => $createdLabTemplateAchievement,
                    'createdLabTemplateAssociations'      => $createdLabTemplateAssociations,
                ];
            });
            if (
                $createdTemplateLab['createdLabTemplate'] &&
                $createdTemplateLab['createdLabTemplateAddress'] &&
                $createdTemplateLab['createdLabTemplateSkillAssociations'] &&
                $createdTemplateLab['createdLabTemplateTagAssociations'] &&
                $createdTemplateLab['createdLabTemplateExternalLinks'] &&
                $createdTemplateLab['createdLabTemplateAchievement'] &&
                $createdTemplateLab['createdLabTemplateAssociations']
            ) {
                DB::commit();

                return $createdTemplateLab['createdLabTemplate'];
            }

            DB::rollBack();

            return false;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function getLabTemplateBasedOnSlug($slug){
        try{
            return $this->labTemplateService->getLabTemplateBasedOnSlug($slug);
        }catch(\Exception $e){
            return false;
        }
    }
}
