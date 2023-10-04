<?php

namespace App\Repositories\Api\Manage\ChallengePath;

use App\Services\Manage\ChallengePathAchievementsService;
use App\Services\Manage\ChallengePathService;
use App\Services\Manage\ChallengePathSkillsGroupsStackService;
use App\Services\Manage\ChallengePathTagsGroupsService;
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

    public function __construct(ChallengePathService $challengePathService, ChallengePathAchievementsService $challengePathAchievementsService ,ChallengePathSkillsGroupsStackService $challengePathSkillsGroupsStackService, ChallengePathTagsGroupsService $challengePathTagsGroupsService, ComponentAssociationService $componentAssociationService)
    {
        $this->challengePathService = $challengePathService;
        $this->challengePathSkillsGroupsStackService = $challengePathSkillsGroupsStackService;
        $this->challengePathAchievementsService = $challengePathAchievementsService;
        $this->challengePathTagsGroupsService = $challengePathTagsGroupsService;
        $this->componentAssociationService = $componentAssociationService;
    }

    public function uploadChallengePathMedia($image)
    {
        try {
            return $this->challengePathService->uploadChallengePathMedia($image);
        } catch (Exception $e) {
            return false;
        }
    }

    public function uploadAchievementImage($achievementImage)
    {
        try {
            return $this->challengePathAchievementsService->uploadAchievementImage($achievementImage);
        } catch (Exception $e) {
            return false;
        }
    }

    public function createChallengePath($upload_cover_image, $upload_achievement_image, $request)
    {
        try {
            $createChallengePath = DB::transaction(function () use ($upload_cover_image, $upload_achievement_image, $request) {
                $createdChallengePathAchievement = true;
                $createdChallengePath = $this->challengePathService->createChallengePath($upload_cover_image, $request);
                if ($request->is_achievement_enabled == 'yes') {
                    $createdChallengePathAchievement = $this->challengePathAchievementsService->createChallengePathAchievement($request, $createdChallengePath->id, $upload_achievement_image);
                }
                $challengePathSkillsGroupsStack = $this->challengePathSkillsGroupsStackService->createChallengePathSkillsGroupsStack($request, $createdChallengePath->id);
                $challengePathTagsGroupsService = $this->challengePathTagsGroupsService->createChallengePathTagsGroupsService($request, $createdChallengePath->id);
                $componentAssociation = $this->componentAssociationService->challengePathAssociation($request, $createdChallengePath->id);

                return [
                    'createdChallengePath'              => $createdChallengePath,
                    'createdChallengePathAchievement'   => $createdChallengePathAchievement,
                    'challengePathSkillsGroupsStack'    => $challengePathSkillsGroupsStack,
                    'challengePathTagsGroupsService'    => $challengePathTagsGroupsService,
                    'componentAssociation'              => $componentAssociation,
                ];
            });

            if (
                $createChallengePath['createdChallengePath'] &&
                $createChallengePath['createdChallengePathAchievement'] &&
                $createChallengePath['challengePathSkillsGroupsStack'] &&
                $createChallengePath['challengePathTagsGroupsService'] &&
                $createChallengePath['componentAssociation']
                )
                {
                    DB::commit();
                    return $createChallengePath['createdChallengePath'];
                }
                DB::rollback();
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
