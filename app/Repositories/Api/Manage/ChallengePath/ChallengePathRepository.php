<?php

namespace App\Repositories\Api\Manage\ChallengePath;

use App\Helpers\UtilityHelper;
use App\Services\Manage\ChallengePathAchievementsService;
use App\Services\Manage\ChallengePathService;
use App\Services\Manage\ChallengePathSkillsGroupsStackService;
use App\Services\Manage\ChallengePathTagsGroupsService;
use App\Services\Manage\ChallengePathTypeModeService;
use App\Services\Manage\ComponentAssociationService;
use Exception;
use Illuminate\Support\Facades\DB;

class ChallengePathRepository implements ChallengePathInterface
{
    private $challengePathService;
    private $challengePathSkillsGroupsStackService;
    private $challengePathAchievementsService;
    private $challengePathTagsGroupsService;
    private $componentAssociationService;
    private $challengePathTypeModeService;

    public function __construct(ChallengePathTypeModeService $challengePathTypeModeService,ChallengePathService $challengePathService, ChallengePathAchievementsService $challengePathAchievementsService, ChallengePathSkillsGroupsStackService $challengePathSkillsGroupsStackService, ChallengePathTagsGroupsService $challengePathTagsGroupsService, ComponentAssociationService $componentAssociationService)
    {
        $this->challengePathService = $challengePathService;
        $this->challengePathSkillsGroupsStackService = $challengePathSkillsGroupsStackService;
        $this->challengePathAchievementsService = $challengePathAchievementsService;
        $this->challengePathTagsGroupsService = $challengePathTagsGroupsService;
        $this->componentAssociationService = $componentAssociationService;
        $this->challengePathTypeModeService=$challengePathTypeModeService;
    }

    public function getChallengePathCountBasedOnOrganization($organizationId)
    {
        try {
            return $this->challengePathService->getChallengePathCountBasedOnOrganization($organizationId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getChallengePathList($request, $organization)
    {
        try {
            return $this->challengePathService->getChallengePathList($request, $organization);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getChallengePathBasedOnSlug($slug)
    {
        try {
            return $this->challengePathService->getChallengePathBasedOnSlug($slug);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function uploadChallengePathMedia($image)
    {
        try {
            return $this->challengePathService->uploadChallengePathMedia($image);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function uploadAchievementImage($achievementImage)
    {
        try {
            return $this->challengePathAchievementsService->uploadAchievementImage($achievementImage);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createChallengePath($upload_cover_image, $upload_achievement_image, $request, $organizationId)
    {
        try {
            $createChallengePath = DB::transaction(function () use ($upload_cover_image, $upload_achievement_image, $request, $organizationId) {
                $createdChallengePathAchievement = true;
                $createdChallengePath = $this->challengePathService->createChallengePath($upload_cover_image, $request, $organizationId);
                if ($request->is_achievement_enabled == 'yes') {
                    $createdChallengePathAchievement = $this->challengePathAchievementsService->createChallengePathAchievement($request, $createdChallengePath->id, $upload_achievement_image);
                }
                $createdChallengePathSkillsGroupsStack = $this->challengePathSkillsGroupsStackService->createChallengePathSkillsGroupsStack($request, $createdChallengePath->id);
                $createdComponentAssociation = $this->componentAssociationService->createChallengePathAssociation($request, $createdChallengePath->id);
                $challengePathTypeModeService=$this->challengePathTypeModeService->createUpdateChallengePathTypeModels($request,$createdChallengePath->id);
                return [
                    'createdChallengePath'                     => $createdChallengePath,
                    'createdChallengePathAchievement'          => $createdChallengePathAchievement,
                    'createdChallengePathSkillsGroupsStack'    => $createdChallengePathSkillsGroupsStack,
                    'createdComponentAssociation'              => $createdComponentAssociation,
                    'challengePathTypeModeService'              => $challengePathTypeModeService,
                ];
            });

            if (
                $createChallengePath['createdChallengePath'] &&
                $createChallengePath['createdChallengePathAchievement'] &&
                $createChallengePath['createdChallengePathSkillsGroupsStack'] &&
                $createChallengePath['challengePathTypeModeService'] &&
                $createChallengePath['createdComponentAssociation']
            ) {
                DB::commit();

                return $createChallengePath['createdChallengePath'];
            }
            DB::rollback();

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateChallengePath($slug, $request, $upload_cover_image, $upload_achievement_image, $organizationId)
    {
        try {
            $updateChallengePath = DB::transaction(function () use ($slug, $request, $upload_cover_image, $upload_achievement_image, $organizationId) {
                $updateChallengePath = $this->challengePathService->updateChallengePath($slug, $request, $upload_cover_image, $organizationId);
                $updateChallengePathAchievement = true;
                if ($request->is_achievement_enabled == 'yes') {
                    $updateChallengePathAchievement = $this->challengePathAchievementsService->updateChallengePathAchievement($request, $updateChallengePath->id, $upload_achievement_image);
                }
                $updateChallengePathSkillsGroupsStack = $this->challengePathSkillsGroupsStackService->updateChallengePathSkillsGroupsStack($request, $updateChallengePath->id);
                $updateComponentAssociation = $this->componentAssociationService->updateChallengePathAssociation($request, $updateChallengePath->id);
                $updatePathTypeModeService=$this->challengePathTypeModeService->createUpdateChallengePathTypeModels($request,$updateChallengePath->id);

                return [
                    'updateChallengePath'                     => $updateChallengePath,
                    'updateChallengePathAchievement'          => $updateChallengePathAchievement,
                    'updateChallengePathSkillsGroupsStack'    => $updateChallengePathSkillsGroupsStack,
                    'updateComponentAssociation'              => $updateComponentAssociation,
                    'updatePathTypeModeService'               => $updatePathTypeModeService,
                ];
            });

            if (
                $updateChallengePath['updateChallengePath'] &&
                $updateChallengePath['updateChallengePathAchievement'] &&
                $updateChallengePath['updateChallengePathSkillsGroupsStack'] &&
                $updateChallengePath['updatePathTypeModeService'] &&
                $updateChallengePath['updateComponentAssociation']
            ) {
                DB::commit();

                return $updateChallengePath['updateChallengePath'];
            }
            DB::rollback();

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkSlug($slug)
    {
        try {
            $checkChallengePathSlug = $this->challengePathService->checkSlug($slug);

            return $checkChallengePathSlug;
        } catch(Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkNameExistsOrNot($title)
    {
        try {
            return $this->challengePathService->checkNameExistsOrNot($title);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function delete($challengePathId)
    {
        try {
            return $this->challengePathService->delete($challengePathId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getChallengePathListName($request, $organization)
    {
        try {
            return $this->challengePathService->getChallengePathListName($request, $organization);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
