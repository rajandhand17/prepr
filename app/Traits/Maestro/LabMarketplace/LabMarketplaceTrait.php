<?php

namespace App\Traits\Maestro\LabMarketplace;

use App\Helpers\UtilityHelper;
use App\Services\Maestro\LabMarketplaceService;
use App\Services\Maestro\LabService;
use App\Services\Maestro\LabMarketplaceAddressService;
use App\Services\Maestro\LabMarketplaceSkillsGroupStackService;
use App\Services\Maestro\LabMarketplaceTagsGroupsService;
use App\Services\Maestro\LabMarketplaceExternalLinksService;
use App\Services\Maestro\LabMarketplaceAchievementsService;
use App\Services\Maestro\LabMarketplaceComponentAssociationService;
use Illuminate\Support\Facades\DB;
trait LabMarketplaceTrait
{
    private function getLabMarketplace()
    {
        try {
            $labMarketplace = $this->labMarketplaceService->getLabMarketplace();
            if ($labMarketplace) {
                return $labMarketplace;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteLabMarketplaceById($id)
    {
        try {
            $slug = $this->labMarketplaceService->getLabMarketplaceBasedOnId($id)->slug;
            $deleteLabMarketplace = $this->labMarketplaceService->deleteLabMarketplace($slug, $id);
            if ($deleteLabMarketplace) {
                return $deleteLabMarketplace;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabMarketplaceById($id)
    {
        try {
            $labMarketplace = $this->labMarketplaceService->getLabMarketplaceBasedOnId($id);
            if ($labMarketplace) {
                return $labMarketplace;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabBasedOnSlug($slug)
    {
        try {
            return LabService::getLabBasedOnSlug($slug);
        }catch (\Exception $e) {
            return false;
        }
    }

    public function addLabToMarketplace($slug, $labId)
    {
        try {
            $addLabMarketplace = DB::transaction(function () use ($slug, $labId) {
                $addLabToMarketplace                = LabMarketplaceService::addLabToMarketplace($slug);
                $addLabMarketplaceAddress           = LabMarketplaceAddressService::addLabMarketplaceAddress($addLabToMarketplace->id, $labId);
                $addLabMarketplaceSkillAssociations = LabMarketplaceSkillsGroupStackService::addLabMarketplaceSkillsGroupsStack($addLabToMarketplace->id, $labId);
                $addLabMarketplaceTagAssociations   = LabMarketplaceTagsGroupsService::addLabMarketplaceTagsGroup($addLabToMarketplace->id, $labId);
                $addLabMarketplaceExternalLinks     = LabMarketplaceExternalLinksService::addLabMarketplaceExternalLinks($addLabToMarketplace->id, $labId);
                $addLabMarketplaceAchievement       = LabMarketplaceAchievementsService::addLabMarketplaceAchievements($addLabToMarketplace->id, $labId);
                $addLabMarketplaceAssociations      = LabMarketplaceComponentAssociationService::addLabMarketplaceComponentAssociation($addLabToMarketplace->id, $labId);
                $updateLab                          = LabService::updatePreBuilt($labId, '1');
                return[
                    'labMarketplace'                            => $addLabToMarketplace,
                    'addLabMarketplaceAddress'                  => $addLabMarketplaceAddress,
                    'addLabMarketplaceSkillAssociations'        => $addLabMarketplaceSkillAssociations,
                    'addLabMarketplaceTagAssociations'          => $addLabMarketplaceTagAssociations,
                    'addLabMarketplaceExternalLinks'            => $addLabMarketplaceExternalLinks,
                    'addLabMarketplaceAchievement'              => $addLabMarketplaceAchievement,
                    'addLabMarketplaceAssociations'             => $addLabMarketplaceAssociations,
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
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();

            return false;
        }
    }
}
