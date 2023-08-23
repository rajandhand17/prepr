<?php

namespace App\Repositories\Api\Manage\Lab;

use App\Services\DurationService;
use App\Services\Manage\ComponentAssociationService;
use App\Services\Manage\LabAcheivementService;
use App\Services\Manage\LabAddressService;
use App\Services\Manage\LabExternalLinksService;
use App\Services\Manage\LabService;
use App\Services\Manage\LabSkillsGroupsStackService;
use App\Services\Manage\LabTagsGroupsService;
use App\Services\Manage\MemberManagementService;
use App\Services\SkillService;
use DB;

class LabRepository implements LabInterface
{
    private $labService;
    private $memberManagementService;
    private $labAddressService;
    private $labExternalLinksService;
    private $labSkillsGroupsStackService;
    private $labTagsGroupsService;
    private $labAcheivementService;
    private $skillService;
    private $componentAssociationService;

    private $durationService;

    public function __construct(LabService $labService, MemberManagementService $memberManagementService, LabAddressService $labAddressService, LabExternalLinksService $labExternalLinksService, LabSkillsGroupsStackService $labSkillsGroupsStackService, LabTagsGroupsService $labTagsGroupsService, LabAcheivementService $labAcheivementService, SkillService $skillService, ComponentAssociationService $componentAssociationService, DurationService $durationService)
    {
        $this->labService = $labService;
        $this->memberManagementService = $memberManagementService;
        $this->labAddressService = $labAddressService;
        $this->labExternalLinksService = $labExternalLinksService;
        $this->labSkillsGroupsStackService = $labSkillsGroupsStackService;
        $this->labTagsGroupsService = $labTagsGroupsService;
        $this->labAcheivementService = $labAcheivementService;
        $this->skillService = $skillService;
        $this->componentAssociationService = $componentAssociationService;
        $this->durationService = $durationService;
    }

    public function getLabList($request, $organization)
    {
        try {
            return $this->labService->getLabList($request, $organization);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabBasedOnSlug($slug)
    {
        try {
            return $this->labService->getLabBasedOnSlug($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function uploadLabCoverImage($image)
    {
        try {
            return $this->labService->uploadLabCoverImage($image);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createLab($request, $upload_profile_image, $upload_achievements_image)
    {
        try {
            $createdLab = DB::transaction(function () use ($request, $upload_profile_image, $upload_achievements_image) {
                $createLab = $this->labService->createLab($request, $upload_profile_image);
                $createdLabAddress = $this->labAddressService->createLabAddress($request, $createLab);
                $createdLabSkillAssociations = $this->labSkillsGroupsStackService->createLabSkillsGroupsStack($request, $createLab);
                $createdLabTagAssociations = $this->labTagsGroupsService->createLabTagsGroups($request, $createLab);
                $createdLabExternalLinks = $this->labExternalLinksService->createLabExternalLinks($request, $createLab);
                if ($request->is_achievement_enabled == 'yes') {
                    $createdLabAchievement = $this->labAcheivementService->createLabAchievement($request, $createLab, $upload_achievements_image);
                }
                $createdLabAssociations = $this->componentAssociationService->labAssociation($request, $createLab);

                return [
                    'createdLab'                  => $createLab,
                    'createdLabAddress'           => $createdLabAddress,
                    'createdLabSkillAssociations' => $createdLabSkillAssociations,
                    'createdLabTagAssociations'   => $createdLabTagAssociations,
                    'createdLabExternalLinks'     => $createdLabExternalLinks,
                    'createdLabAchievement'       => ($request->is_achievement_enabled == 'yes') ? $createdLabAchievement : true,
                    'createdLabAssociations'      => $createdLabAssociations,
                ];
            });
            if (
                $createdLab['createdLab'] &&
                $createdLab['createdLabAddress'] &&
                $createdLab['createdLabSkillAssociations'] &&
                $createdLab['createdLabTagAssociations'] &&
                $createdLab['createdLabExternalLinks'] &&
                $createdLab['createdLabAchievement'] &&
                $createdLab['createdLabAssociations']
            ) {
                DB::commit();

                return $createdLab['createdLab'];
            }
            DB::rollBack();

            return false;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function updateLab($slug, $request, $upload_cover_image, $upload_achievement_image)
    {
        try {
            $updatedLab = DB::transaction(function () use ($slug, $request, $upload_cover_image, $upload_achievement_image) {
                $updateLab = $this->labService->updateLab($slug, $request, $upload_cover_image);
                $updatedLabAddress = $this->labAddressService->updateLabAddress($request, $updateLab->id);
                $updatedLabSkillAssociations = $this->labSkillsGroupsStackService->updateLabSkillsGroupsStack($request, $updateLab->id);
                $updatedLabTagAssociations = $this->labTagsGroupsService->updateLabTagsGroups($request, $updateLab->id);

                $updatedLabExternalLinks = $this->labExternalLinksService->updateLabExternalLinks($request, $updateLab->id);

                if ($request->is_achievement_enabled == 'yes') {
                    $updatedLabAchievement = $this->labAcheivementService->updateLabAchievement($request, $updateLab->id, $upload_achievement_image);
                }
                $updatedLabAssociations = $this->componentAssociationService->updateLabAssociation($request, $updateLab->id);

                return [
                    'updatedLab'                  => $updateLab,
                    'updatedLabAddress'           => $updatedLabAddress,
                    'updatedLabSkillAssociations' => $updatedLabSkillAssociations,
                    'updatedLabTagAssociations'   => $updatedLabTagAssociations,
                    'updatedLabExternalLinks'     => $updatedLabExternalLinks,
                    'updatedLabAchievement'       => ($request->is_achievement_enabled == 'yes') ? $updatedLabAchievement : true,
                    'updatedLabAssociations'      => $updatedLabAssociations,
                ];
            });
            if (
                $updatedLab['updatedLab'] &&
                $updatedLab['updatedLabAddress'] &&
                $updatedLab['updatedLabSkillAssociations'] &&
                $updatedLab['updatedLabTagAssociations'] &&
                $updatedLab['updatedLabExternalLinks'] &&
                $updatedLab['updatedLabAchievement'] &&
                $updatedLab['updatedLabAssociations']
            ) {
                DB::commit();

                return $updatedLab['updatedLab'];
            }
            DB::rollBack();

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteLab($lab_id, $request)
    {
        try {
            DB::beginTransaction();

            $deleteLab = $this->labService->deleteLab($lab_id);
            if ($deleteLab == false) {
                DB::rollBack();

                return false;
            }
            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function checkSlug($slug)
    {
        try {
            return $this->labService->getLabBasedOnSlug($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkNameExistsOrNot($title)
    {
        try {
            $labSlug = $this->labService->checkNameExistsOrNot($title);

            return $labSlug;
        } catch (\Exception $e) {
            return false;
        }
    }
}
