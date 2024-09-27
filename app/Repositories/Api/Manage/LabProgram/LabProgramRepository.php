<?php

namespace App\Repositories\Api\Manage\LabProgram;

use App\Helpers\UtilityHelper;
use App\Services\Manage\ComponentAssociationService;
use App\Services\Manage\LabProgramAchievementsService;
use App\Services\Manage\LabProgramService;
use App\Services\Manage\LabProgramSkillsGroupsStackService;
use App\Services\Manage\LabProgramTagsGroupsService;
use App\Services\Manage\LabProgramTypeModesService;
use App\Services\Public\FeaturedModuleService;
use Exception;
use Illuminate\Support\Facades\DB;

class LabProgramRepository implements LabProgramInterface
{
    private $labProgramService;

    private $labProgramAchievementService;

    private $labProgramSkillsGroupsStackService;

    private $labProgramTagsGroupsService;

    private $componentAssociationService;

    private $labProgramTypeModesService;
    private $featuredModuleService;

    public function __construct(FeaturedModuleService $featuredModuleService, LabProgramService $labProgramService, LabProgramAchievementsService $labProgramAchievementService, LabProgramSkillsGroupsStackService $labProgramSkillsGroupsStackService, LabProgramTagsGroupsService $labProgramTagsGroupsService, ComponentAssociationService $componentAssociationService, LabProgramTypeModesService $labProgramTypeModesService)
    {
        $this->labProgramService = $labProgramService;
        $this->labProgramAchievementService = $labProgramAchievementService;
        $this->labProgramSkillsGroupsStackService = $labProgramSkillsGroupsStackService;
        $this->labProgramTagsGroupsService = $labProgramTagsGroupsService;
        $this->componentAssociationService = $componentAssociationService;
        $this->labProgramTypeModesService = $labProgramTypeModesService;
        $this->featuredModuleService = $featuredModuleService;
    }

    public function getLabProgramCountBasedOnOrganization($organizationId)
    {
        try {
            return $this->labProgramService->getLabProgramCountBasedOnOrganization($organizationId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getLabProgramList($request, $organization)
    {
        try {
            return $this->labProgramService->getLabProgramList($request, $organization);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getLabProgramBasedOnSlug($slug)
    {
        try {
            return $this->labProgramService->getLabProgramBasedOnSlug($slug);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function uploadLabProgramMedia($slug)
    {
        try {
            return $this->labProgramService->uploadLabProgramMedia($slug);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createLabProgram($request, $upload_media, $upload_achievement_image, $organizationId)
    {
        try {
            $createLabProgram = DB::transaction(function () use ($request, $upload_media, $upload_achievement_image, $organizationId) {
                $createdLabProgram = $this->labProgramService->createLabProgram($request, $upload_media, $organizationId);
                $labProgramSkillsGroupsStack = $this->labProgramSkillsGroupsStackService->createLabProgramSkillsGroupsStack($request, $createdLabProgram->id);
                $labProgramTypeModesStore = $this->labProgramTypeModesService->labProgramTypeModes($request, $createdLabProgram->id);
                if ($request->is_achievement_enabled == 'yes') {
                    $labProgramAchievement = $this->labProgramAchievementService->createLabProgramAchievement($request, $createdLabProgram->id, $upload_achievement_image);
                }
                $componentAssociation = $this->componentAssociationService->labProgramAssociation($request, $createdLabProgram);

                return [
                    'createLabProgram'           => $createdLabProgram,
                    'labProgramSkillsGroupsStack'=> $labProgramSkillsGroupsStack,
                    'componentAssociation'       => $componentAssociation,
                    'labProgramTypeModesStore'   => $labProgramTypeModesStore,
                ];
            });

            if ($createLabProgram['createLabProgram'] && $createLabProgram['labProgramSkillsGroupsStack'] && $createLabProgram['labProgramTypeModesStore'] && $createLabProgram['componentAssociation']) {
                DB::commit();

                return $createLabProgram['createLabProgram'];
            }
            DB::rollback();

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();

            return false;
        }
    }

    public function updateLabProgram($slug, $request, $upload_media, $upload_achievement_image, $organizationId)
    {
        try {
            $createLabProgram = DB::transaction(function () use ($slug, $request, $upload_media, $upload_achievement_image, $organizationId) {
                $updateLabProgram = $this->labProgramService->updateLabProgram($slug, $request, $upload_media, $organizationId);
                $labProgramTypeModesUpdate = $this->labProgramTypeModesService->labProgramTypeModes($request, $updateLabProgram->id);
                if ($request->is_achievement_enabled == 'yes') {
                    $labProgramAchievement = $this->labProgramAchievementService->updateLabProgramAchievement($request, $updateLabProgram->id, $upload_achievement_image);
                }
                $labProgramSkillsGroupsStack = $this->labProgramSkillsGroupsStackService->updateLabProgramSkillsGroupsStack($request, $updateLabProgram->id);
                $componentAssociation = $this->componentAssociationService->updateLabProgramAssociation($request, $updateLabProgram->id);

                return [
                    'updateLabProgram'            => $updateLabProgram,
                    'labProgramSkillsGroupsStack' => $labProgramSkillsGroupsStack,
                    'componentAssociation'        => $componentAssociation,
                    'labProgramTypeModesUpdate'   => $labProgramTypeModesUpdate,
                ];
            });
            if ($createLabProgram['updateLabProgram'] && $createLabProgram['componentAssociation'] && $createLabProgram['labProgramSkillsGroupsStack'] && $createLabProgram['labProgramTypeModesUpdate']) {
                DB::commit();

                return $createLabProgram['updateLabProgram'];
            }
            DB::rollback();

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkSlug($slug)
    {
        try {
            $checkLabProgramSlug = $this->labProgramService->checkSlug($slug);

            return $checkLabProgramSlug;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $delteLabPrograms = $this->labProgramService->delete($id);
            $featuredModule = $this->featuredModuleService->deleteFeaturedModule('1', $id);
            if ($delteLabPrograms == false && $featuredModule == false) {
                DB::rollBack();

                return false;
            }
            DB::commit();

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkNameExistsOrNot($title)
    {
        try {
            return $this->labProgramService->checkNameExistsOrNot($title);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getLabProgramListName($request, $organization)
    {
        try {
            return $this->labProgramService->getLabProgramListName($request, $organization);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
