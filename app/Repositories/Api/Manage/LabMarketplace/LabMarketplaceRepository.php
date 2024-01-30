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
use Exception;

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
        } catch (Exception $e) {
            return false;
        }
    }

    public function getCheckLabUuid($uuid)
    {
        try {
            return $this->labMarketplaceService->getCheckLabUuid($uuid);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getOrganizationIdBasedOnUuid($uuid)
    {
        try {
            return $this->organizationService->getOrganizationExistBasedOnUuid($uuid);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getLabMarketplaceBasedOnSlug($slug)
    {
        try {
            return $this->labMarketplaceService->getLabMarketplaceBasedOnSlug($slug);
        } catch (Exception $e) {
            return false;
        }
    }

    public function addLabToMarketplace($slug, $labId)
    {
        try {
            $addLabMarketplace = DB::transaction(function () use ($slug, $labId) {
                $addLabToMarketplace = $this->labMarketplaceService->addLabToMarketplace($slug);
                $addLabMarketplaceAddress = $this->labMarketplaceAddressService->addLabMarketplaceAddress($addLabToMarketplace->id, $labId);
                $addLabMarketplaceSkillAssociations = $this->labMarketplaceSkillsGroupStackService->addLabMarketplaceSkillsGroupsStack($addLabToMarketplace->id, $labId);
                $addLabMarketplaceTagAssociations = $this->labMarketplaceTagsGroupsService->addLabMarketplaceTagsGroupsStack($addLabToMarketplace->id, $labId);
                $addLabMarketplaceExternalLinks = $this->labMarketplaceExternalLinksService->addLabMarketplaceExternalLinks($addLabToMarketplace->id, $labId);
                $addLabMarketplaceAchievement = $this->labMarketplaceAchievementsService->addLabMarketplaceAchievements($addLabToMarketplace->id, $labId);
                $addLabMarketplaceAssociations = $this->labMarketplaceComponentAssociationService->addLabMarketplaceComponentAssociation($addLabToMarketplace->id, $labId);
                $updateLab = $this->labService->updatePreBuilt($labId, '1');

                return[
                    'labMarketplace'                            => $addLabToMarketplace,
                    'addLabMarketplaceAddress'              => $addLabMarketplaceAddress,
                    'addLabMarketplaceSkillAssociations'    => $addLabMarketplaceSkillAssociations,
                    'addLabMarketplaceTagAssociations'      => $addLabMarketplaceTagAssociations,
                    'addLabMarketplaceExternalLinks'        => $addLabMarketplaceExternalLinks,
                    'addLabMarketplaceAchievement'          => $addLabMarketplaceAchievement,
                    'addLabMarketplaceAssociations'         => $addLabMarketplaceAssociations,
                    'updateLab'                                 => $updateLab,
                ];
            });
            if ($addLabMarketplace['labMarketplace'] &&
                $addLabMarketplace['addLabMarketplaceAddress'] &&
                $addLabMarketplace['addLabMarketplaceSkillAssociations'] &&
                $addLabMarketplace['addLabMarketplaceTagAssociations'] &&
                $addLabMarketplace['addLabMarketplaceExternalLinks'] &&
                $addLabMarketplace['addLabMarketplaceAchievement'] &&
                $addLabMarketplace['addLabMarketplaceAssociations'] &&
                $addLabMarketplace['updateLab']) {
                DB::commit();

                return $addLabMarketplace['labMarketplace'];
            }
            DB::rollback();

            return false;
        } catch (Exception $e) {
            DB::rollback();

            return false;
        }
    }

    public function deleteLabMarketplace($slug, $labMarketplaceId)
    {
        try {
            return $this->labMarketplaceService->deleteLabMarketplace($slug, $labMarketplaceId);
        } catch (Exception $e) {
            return false;
        }
    }
}
