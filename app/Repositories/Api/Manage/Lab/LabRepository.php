<?php

namespace App\Repositories\Api\Manage\Lab;

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

    public function __construct(LabService $labService, MemberManagementService $memberManagementService, LabAddressService $labAddressService, LabExternalLinksService $labExternalLinksService, LabSkillsGroupsStackService $labSkillsGroupsStackService, LabTagsGroupsService $labTagsGroupsService, LabAcheivementService $labAcheivementService, SkillService $skillService, ComponentAssociationService $componentAssociationService)
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
    }

    public function getLabList($request)
    {
        try {
            return $this->labService->getLabList($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabBasedOnSlug($slug)
    {
        try {
            return $this->labService->getLabBasedOnSLug($slug);
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
                $createdLab = $this->labService->createLab($request, $upload_profile_image);
                $createdLabAddress = $this->labAddressService->createLabAddress($request, $createdLab);
                $createdLabSkillAssociations = $this->labSkillsGroupsStackService->createLabSkillsGroupsStack($request, $createdLab);
                $createdLabTagAssociations = $this->labTagsGroupsService->createLabTagsGroups($request, $createdLab);
                $createdLabExternalLinks = $this->labExternalLinksService->createLabExternalLinks($request, $createdLab);
                if ($request->is_achievement_enabled == 'yes') {
                    $createdLabAcheivement = $this->labAcheivementService->createLabAchievement($request, $createdLab, $upload_achievements_image);
                }
                $createdLabAssociations = $this->componentAssociationService->labAssociation($request, $createdLab);
                return $createdLab;
            });
            if ($createdLab) {
                DB::commit();
                return $createdLab;
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
                $updatedLab = $this->labService->updateLab($slug, $request, $upload_cover_image);
                $updatedLabAddress = $this->labAddressService->updateLabAddress($request, $updatedLab->id);
                $updatedLabSkillAssociations = $this->labSkillsGroupsStackService->updateLabSkillsGroupsStack($request, $updatedLab->id);
                $updatedLabTagAssociations = $this->labTagsGroupsService->updateLabTagsGroups($request, $updatedLab->id);
                $updatedLabExternalLinks = $this->labExternalLinksService->updateLabExternalLinks($request, $updatedLab->id);

                if ($request->is_achievement_enabled == 'yes') {
                    $updatedLabAcheivement = $this->labAcheivementService->updateLabAchievement($request, $updatedLab->id, $upload_achievement_image);
                }
                $updatedLabAssociations = $this->componentAssociationService->updateLabAssociation($request, $updatedLab->id);

                return $updatedLab;
            });
            if ($updatedLab) {
                DB::commit();

                return $updatedLab;
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
            return $this->labService->getLabBasedOnSLug($slug);
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
