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

    public function createLab($request, $upload_profile_image, $upload_acheivements_image)
    {
        try {
            DB::beginTransaction();
            $createdLab = $this->labService->createLab($request, $upload_profile_image);
            if ($createdLab !== false) {
                $createdLabAddress = $this->labAddressService->createLabAddress($request, $createdLab);
                if ($createdLabAddress == false) {
                    DB::rollBack();

                    return false;
                }
                $createdLabSkillAssociations = $this->labSkillsGroupsStackService->createLabSkillsGroupsStack($request, $createdLab);

                if ($createdLabSkillAssociations == false) {
                    DB::rollBack();

                    return false;
                }

                $createdLabTagAssociations = $this->labTagsGroupsService->createLabTagsGroups($request, $createdLab);
                if ($createdLabTagAssociations == false) {
                    DB::rollBack();

                    return false;
                }

                $createdLabExternalLinks = $this->labExternalLinksService->createLabExternalLinks($request, $createdLab);
                if ($createdLabExternalLinks == false) {
                    DB::rollBack();

                    return false;
                }

                if ($request->is_achievement_enabled == 'yes') {
                    $createdLabAcheivement = $this->labAcheivementService->createLabAchievement($request, $createdLab, $upload_acheivements_image);
                    if ($createdLabAcheivement == false) {
                        DB::rollBack();

                        return false;
                    }
                }

                $createdLabAssociations = $this->componentAssociationService->labAssociation($request, $createdLab);
                if ($createdLabAssociations == false) {
                    DB::rollBack();

                    return false;
                }
//                $memberList = [];
//                if ($request->invite_type == 'csv') {
//                    $memberList = $this->memberManagementService->getRecordsFromCsv($request);
//                    if (!$memberList && !count($memberList) > 0) {
//                        return false;
//                    }
//                }
//                if ($request->invite_type == 'email') {
//                    $memberList = $this->memberManagementService->getRecordsFromEmailArray($request);
//                    if (!$memberList && !count($memberList) > 0) {
//                        return false;
//                    }
//                }
//                if (is_array($memberList) && count($memberList) > 0) {
//                    $checkStatus = $this->memberManagementService->addMembers($createdLab, "lab", $request, $memberList);
//                    if ($checkStatus != false) {
//                        return true;
//                    }
//                    return false;
//                }
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
