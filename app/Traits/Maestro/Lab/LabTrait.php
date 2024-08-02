<?php

namespace App\Traits\Maestro\Lab;

use App\Helpers\UtilityHelper;
use App\Services\Maestro\ComponentAssociationService;
use App\Services\Maestro\LabAchievementService;
use App\Services\Maestro\LabAddressService;
use App\Services\Maestro\LabExternalLinksService;
use App\Services\Maestro\LabService;
use App\Services\Maestro\LabSkillsGroupsStackService;
use Exception;
use Illuminate\Support\Facades\DB;

trait LabTrait
{
    protected $labService;

    public function getLabsBasedOnOrganizations($request)
    {
        try {
            $labList = LabService::getLabBasedOnOrganization($request);
            if ($labList) {
                return $labList;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabCountBasedOnOrganization($organizationId)
    {
        try {
            return $this->labService->getLabCountBasedOnOrganization($organizationId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function createLab($request)
    {
        try {
            // Getting Lab and related tables
            $createdLab = DB::transaction(function () use ($request) {
                $newLab = LabService::createLab($request);
                $labAddress = LabAddressService::createLabAddress($request, $newLab->id);
                $labSKillsGroupStack = LabSkillsGroupsStackService::createLabSkillsGroupsStack($request, $newLab->id);
                $labExternalLinks = LabExternalLinksService::createLabExternalLinks($request, $newLab->id);
                $componentAssociation = ComponentAssociationService::labAssociation($request, $newLab->id);
                //   $createdLabAchievement = LabAchievementService::createLabAchievement($newLab->id);

                return [
                    'lab'                    => $newLab,
                    'lab_address'            => $labAddress,
                    'lab_skills_group_stack' => $labSKillsGroupStack,
                    'lab_external_links'     => $labExternalLinks,
                    //  'lab_achievement'        => $createdLabAchievement
                    'componentAssociation' => $componentAssociation,
                ];
            });

            // Checking all the tables records inserted successfully
            if ($createdLab['lab'] && $createdLab['lab_address'] && $createdLab['lab_skills_group_stack']
                 && $createdLab['lab_external_links'] && $createdLab['componentAssociation']) {
                DB::commit();

                // Returning new created table details
                return $createdLab['lab'];
            }
            DB::rollBack();

            return false;
        } catch(Exception $e) {
            DB::rollback();

            return false;
        }
    }

    private function updateLabById($id, $request)
    {
        try {
            // Getting Lab and related tables
            $updatedLab = DB::transaction(function () use ($request, $id) {
                $lab = LabService::updateLabById($request, $id);
                $labAddress = LabAddressService::updateLabAddress($request, $id);
                $labSKillsGroupStack = LabSkillsGroupsStackService::updateLabSkillsGroupsStack($request, $id);
                $labExternalLinks = LabExternalLinksService::updateLabExternalLinks($request, $id);
                $componentAssociation = ComponentAssociationService::updatelabAssociation($request, $id);
                //   $createdLabAchievement = LabAchievementService::createLabAchievement($newLab->id);

                return [
                    'lab'                    => $lab,
                    'lab_address'            => $labAddress,
                    'lab_skills_group_stack' => $labSKillsGroupStack,
                    'lab_external_links'     => $labExternalLinks,
                    //  'lab_achievement'        => $createdLabAchievement
                    'componentAssociation' => $componentAssociation,
                ];
            });
            // Checking all the tables records inserted successfully
            if ($updatedLab['lab'] && $updatedLab['lab_address'] && $updatedLab['lab_skills_group_stack']
                 && $updatedLab['lab_external_links'] && $updatedLab['componentAssociation']) {
                DB::commit();

                // Returning new created table details
                return $updatedLab['lab'];
            }
            DB::rollBack();

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function deleteLabById($id)
    {
        try {
            DB::beginTransaction();
            $deleteLab = labService::deleteLab($id);
            $deleteLabSkills = LabSkillsGroupsStackService::deleteLabSkillsGroupsStack($id);
            $deleteLinks = LabExternalLinksService::deleteLabExternalLinks($id);
            $deleteLabAddress = LabAddressService::deleteLabAddress($id);
            $deleteComponentAssociation = ComponentAssociationService::deletelabAssociation($id);
            if ($deleteLab && $deleteLabSkills && $deleteLinks && $deleteLabAddress && $deleteComponentAssociation == false) {
                DB::rollBack();

                return false;
            }
            DB::commit();

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollBack();

            return false;
        }
    }

    private function getLabAssociatedItemsById($lab)
    {
        try {
            $associateItems = LabService::getLabAssociatedItemsById($lab);
            if ($associateItems) {
                return $associateItems;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getLabExternalLinks($id)
    {
        try {
            $externalLinks = LabExternalLinksService::getLabExternalLinks($id);

            if ($externalLinks) {
                return $externalLinks;
            } else {
                return [];
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
