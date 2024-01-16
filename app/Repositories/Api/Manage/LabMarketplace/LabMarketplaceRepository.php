<?php

namespace App\Repositories\Api\Manage\LabMarketplace;

use App\Services\Manage\LabMarketplaceAchievementsService;
use App\Services\Manage\LabMarketplaceAddressService;
use App\Services\Manage\LabMarketplaceComponentAssociationService;
use App\Services\Manage\LabMarketplaceExternalLinksService;
use App\Services\Manage\LabMarketplaceService;
use App\Services\Manage\LabMarketplaceSkillsGroupStackService;
use App\Services\Manage\LabMarketplaceTagsGroupService;
use App\Services\Manage\LabService;
use App\Services\Manage\OrganizationService;
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

    private $organizationService;

    public function __construct(OrganizationService $organizationService, LabMarketplaceComponentAssociationService $labMarketplaceComponentAssociationService, LabMarketplaceAchievementsService $labMarketplaceAchievementsService, LabMarketplaceExternalLinksService $labMarketplaceExternalLinksService, LabMarketplaceTagsGroupService $labMarketplaceTagsGroupsService, LabMarketplaceSkillsGroupStackService $labMarketplaceSkillsGroupStackService, LabMarketplaceService $labMarketplaceService, LabService $labService, LabMarketplaceAddressService $labMarketplaceAddressService)
    {
        $this->labMarketplaceService = $labMarketplaceService;
        $this->labService = $labService;
        $this->labMarketplaceAddressService = $labMarketplaceAddressService;
        $this->labMarketplaceSkillsGroupStackService = $labMarketplaceSkillsGroupStackService;
        $this->labMarketplaceTagsGroupsService = $labMarketplaceTagsGroupsService;
        $this->labMarketplaceExternalLinksService = $labMarketplaceExternalLinksService;
        $this->labMarketplaceAchievementsService = $labMarketplaceAchievementsService;
        $this->labMarketplaceComponentAssociationService = $labMarketplaceComponentAssociationService;
        $this->organizationService = $organizationService;
    }

    public function getLabBasedOnSlug($slug)
    {
        try {
            return $this->labService->getLabBasedOnSlug($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getCheckUuid($uuid)
    {
        try {
            return $this->labMarketplaceService->getCheckUuid($uuid);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getOrganizationIdBasedOnUuid($uuid)
    {
        try {
            return $this->organizationService->getOrganizationExistBasedOnUuid($uuid);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabMarketplaceBasedOnSlug($slug)
    {
        try {
            return $this->labMarketplaceService->getLabMarketplaceBasedOnSlug($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createLabMarketplace($slug, $labId, $organizationId)
    {
        try {
            $createLabMarketplace = DB::transaction(function () use ($slug, $labId, $organizationId) {
                $createLabMarketplace = $this->labMarketplaceService->createLabMarketplace($slug, $organizationId);
                $createLabMarketplaceAddress = $this->labMarketplaceAddressService->createLabMarketplaceAddress($createLabMarketplace->id, $labId);
                $createdLabMarketplaceSkillAssociations = $this->labMarketplaceSkillsGroupStackService->createLabMarketplaceSkillsGroupsStack($createLabMarketplace->id, $labId);
                $createdLabMarketplaceTagAssociations = $this->labMarketplaceTagsGroupsService->createLabMarketplaceTagsGroupsStack($createLabMarketplace->id, $labId);
                $createdLabMarketplaceExternalLinks = $this->labMarketplaceExternalLinksService->createLabMarketplaceExternalLinks($createLabMarketplace->id, $labId);
                $createdLabMarketplaceAchievement = $this->labMarketplaceAchievementsService->createLabMarketplaceAchievements($createLabMarketplace->id, $labId);
                $createdLabMarketplaceAssociations = $this->labMarketplaceComponentAssociationService->createMarketplaceComponentAssociation($createLabMarketplace->id, $labId);
                $updateLab = $this->labService->updatePreBuilt($labId, '1');

                return[
                    'labMarketplace'                        => $createLabMarketplace,
                    'createLabMarketplaceAddress'           => $createLabMarketplaceAddress,
                    'createdLabMarketplaceSkillAssociations'=> $createdLabMarketplaceSkillAssociations,
                    'createdLabMarketplaceTagAssociations'  => $createdLabMarketplaceTagAssociations,
                    'createdLabMarketplaceExternalLinks'    => $createdLabMarketplaceExternalLinks,
                    'createdLabMarketplaceAchievement'      => $createdLabMarketplaceAchievement,
                    'createdLabMarketplaceAssociations'     => $createdLabMarketplaceAssociations,
                    'updateLab'                             => $updateLab,
                ];
            });
            if ($createLabMarketplace['labMarketplace'] &&
                $createLabMarketplace['createLabMarketplaceAddress'] &&
                $createLabMarketplace['createdLabMarketplaceSkillAssociations'] &&
                $createLabMarketplace['createdLabMarketplaceTagAssociations'] &&
                $createLabMarketplace['createdLabMarketplaceExternalLinks'] &&
                $createLabMarketplace['createdLabMarketplaceAchievement'] &&
                $createLabMarketplace['createdLabMarketplaceAssociations'] &&
                $createLabMarketplace['updateLab']) {
                DB::commit();

                return $createLabMarketplace['labMarketplace'];
            }
            DB::rollback();

            return false;
        } catch (\Exception $e) {
            DB::rollback();

            return false;
        }
    }

    public function deleteLabMarketplace($slug, $labMarketplaceId)
    {
        try {
            return $this->labMarketplaceService->deleteLabMarketplace($slug, $labMarketplaceId);
        } catch (\Exception $e) {
            return false;
        }
    }
}
