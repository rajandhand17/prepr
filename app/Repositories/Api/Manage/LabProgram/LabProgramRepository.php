<?php

namespace App\Repositories\Api\Manage\LabProgram;

use App\Helpers\UtilityHelper;
use App\Services\Manage\ComponentAssociationService;
use App\Services\Manage\LabProgramAchievementsService;
use App\Services\Manage\LabProgramService;
use App\Services\Manage\LabProgramSkillsGroupsStackService;
use App\Services\Manage\LabProgramTagsGroupsService;
use Exception;
use Illuminate\Support\Facades\DB;

class LabProgramRepository implements LabProgramInterface
{
    private $labProgramService;

    private $labProgramAchievementService;

    private $labProgramSkillsGroupsStackService;

    private $labProgramTagsGroupsService;

    private $componentAssociationService;

    public function __construct(LabProgramService $labProgramService, LabProgramAchievementsService $labProgramAchievementService, LabProgramSkillsGroupsStackService $labProgramSkillsGroupsStackService, LabProgramTagsGroupsService $labProgramTagsGroupsService, ComponentAssociationService $componentAssociationService)
    {
        $this->labProgramService = $labProgramService;
        $this->labProgramAchievementService = $labProgramAchievementService;
        $this->labProgramSkillsGroupsStackService = $labProgramSkillsGroupsStackService;
        $this->labProgramTagsGroupsService = $labProgramTagsGroupsService;
        $this->componentAssociationService = $componentAssociationService;
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
                if ($request->is_achievement_enabled == 'yes') {
                    $labProgramAchievement = $this->labProgramAchievementService->createLabProgramAchievement($request, $createdLabProgram->id, $upload_achievement_image);
                }
                $labProgramTagsGroupsService = $this->labProgramTagsGroupsService->createLabProgramTagsGroups($request, $createdLabProgram->id);
                $componentAssociation = $this->componentAssociationService->labProgramAssociation($request, $createdLabProgram);

                return [
                    'createLabProgram'           => $createdLabProgram,
                    'labProgramSkillsGroupsStack'=> $labProgramSkillsGroupsStack,
                    'labProgramTagsGroupsService'=> $labProgramTagsGroupsService,
                    'componentAssociation'       => $componentAssociation,
                ];
            });
            if ($createLabProgram['createLabProgram'] && $createLabProgram['labProgramSkillsGroupsStack'] && $createLabProgram['labProgramTagsGroupsService'] && $createLabProgram['componentAssociation']) {
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
                if ($request->is_achievement_enabled == 'yes') {
                    $labProgramAchievement = $this->labProgramAchievementService->updateLabProgramAchievement($request, $updateLabProgram->id, $upload_achievement_image);
                }
                $labProgramSkillsGroupsStack = $this->labProgramSkillsGroupsStackService->updateLabProgramSkillsGroupsStack($request, $updateLabProgram->id);
                $labProgramTagsGroupsService = $this->labProgramTagsGroupsService->updateLabProgramTagsGroups($request, $updateLabProgram->id);
                $componentAssociation = $this->componentAssociationService->updateLabProgramAssociation($request, $updateLabProgram->id);

                return [
                    'updateLabProgram'           => $updateLabProgram,
                    'labProgramSkillsGroupsStack'=> $labProgramSkillsGroupsStack,
                    'labProgramTagsGroupsService'=> $labProgramTagsGroupsService,
                    'componentAssociation'       => $componentAssociation,
                ];
            });
            if ($createLabProgram['updateLabProgram'] && $createLabProgram['componentAssociation'] && $createLabProgram['labProgramSkillsGroupsStack']) {
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

    public function delete($slug)
    {
        try {
            return $this->labProgramService->delete($slug);
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
