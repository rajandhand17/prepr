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
            $labDetails = $this->labService->checkSlug($slug);

            return $labDetails;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateLab($lab_id, $request, $upload_cover_image, $upload_acheivements_image)
    {
        try {
            DB::beginTransaction();
            $updateLab = $this->labService->updateLab($lab_id, $request, $upload_cover_image);
            if ($updateLab !== false) {
                $updateLabAddress = $this->labAddressService->updateLabAddress($lab_id, $request);
                if ($updateLabAddress == false) {
                    DB::rollBack();

                    return false;
                }
                $updateLabSkillAssociations = $this->labSkillsGroupsStackService->updateLabSkillsGroupsStack($lab_id, $request);
                if ($updateLabSkillAssociations == false) {
                    DB::rollBack();

                    return false;
                }

                $updateLabTagAssociations = $this->labTagsGroupsService->updateLabTagsGroups($lab_id, $request);
                if ($updateLabTagAssociations == false) {
                    DB::rollBack();

                    return false;
                }
                $updateLabExternalLinks = $this->labExternalLinksService->updateLabExternalLinks($lab_id, $request);
                if ($updateLabExternalLinks == false) {
                    DB::rollBack();

                    return false;
                }
                if ($request->is_achievement_enabled == 'yes') {
                    $updateLabAcheivement = $this->labAcheivementService->updateLabAchievement($lab_id, $request, $upload_acheivements_image);
                    if ($updateLabAcheivement == false) {
                        DB::rollBack();

                        return false;
                    }
                }
                $updateLabAssociations = $this->componentAssociationService->updatelabAssociation($lab_id, $request);
                if ($updateLabAssociations == false) {
                    DB::rollBack();

                    return false;
                }
            }

            DB::commit();

            return $updateLab;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteLab($lab_id, $request)
    {
        try {
            DB::beginTransaction();
            if (isset($request->status) && !empty($request->status) && $request->status == 'archived') {
                $archived = $this->labService->archieveLab($lab_id);
                if ($archived == false) {
                    DB::rollBack();
                    return false;
                }
                DB::commit();
                return true;
            }
            $deleteLab = $this->labService->deleteLab($lab_id);
            if ($deleteLab == false) {
                DB::rollBack();
                return false;
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            dd($e);
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

    public function storeLabActivity($activity, $lab_id, $request)
    {
        try {
            $labCheckExistsOrNot = $this->labService->checkExistsOrNot($activity, $lab_id);
            if ($labCheckExistsOrNot == false) {
                $storeLabActivity = $this->labService->storeLabActivity($activity, $lab_id, $request);

                return $storeLabActivity;
            }
            $updateLabActivity = $this->labService->updateLabActivity($activity, $labCheckExistsOrNot->id, $request);

            return $updateLabActivity;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkActivity($activity, $lab_id)
    {
        try {
            return $this->labService->checkActivity($activity, $lab_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateCoverImage($image){
        try {
            return $this->labService->updateCoverImage($image);
        } catch (\Exception $e){
            return false;
        }
    }
}
