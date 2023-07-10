<?php

namespace App\Repositories\Api\Lab;

use App\Services\ComponentAssociationService;
use App\Services\FavoriteService;
use App\Services\LabAcheivementService;
use App\Services\LabAddressService;
use App\Services\LabExternalLinksService;
use App\Services\LabService;
use App\Services\LabSkillsGroupsStackService;
use App\Services\LabTagsGroupsService;
use App\Services\MemberManagementService;
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
    private $favoriteService;
    private $componentAssociationService;

    public function __construct(LabService $labService, MemberManagementService $memberManagementService, LabAddressService $labAddressService, LabExternalLinksService $labExternalLinksService, LabSkillsGroupsStackService $labSkillsGroupsStackService, LabTagsGroupsService $labTagsGroupsService, LabAcheivementService $labAcheivementService, SkillService $skillService, FavoriteService $favoriteService, ComponentAssociationService $componentAssociationService)
    {
        $this->labService = $labService;
        $this->memberManagementService = $memberManagementService;
        $this->labAddressService = $labAddressService;
        $this->labExternalLinksService = $labExternalLinksService;
        $this->labSkillsGroupsStackService = $labSkillsGroupsStackService;
        $this->labTagsGroupsService = $labTagsGroupsService;
        $this->labAcheivementService = $labAcheivementService;
        $this->skillService = $skillService;
        $this->favoriteService = $favoriteService;
        $this->componentAssociationService = $componentAssociationService;
    }

    public function uploadCoverImage($image)
    {
        try {
            return $this->labService->uploadCoverImage($image);
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
        } catch (\Throwable $e) {
            DB::rollback();

            return false;
        }
    }

    public function getLabList($request)
    {
        try {
            $lab = $this->labService->getLabList($request);
            if ($lab) {
                return $lab;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabDetails($slug)
    {
        try {
            $labDetails = $this->labService->getLabDetails($slug);

            return $labDetails;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkSlug($slug)
    {
        try {
            $labSlug = $this->labService->checkSlug($slug);

            return $labSlug;
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
