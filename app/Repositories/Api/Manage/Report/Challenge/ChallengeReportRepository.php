<?php

namespace App\Repositories\Api\Manage\Report\Challenge;

use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use App\Models\ChallengePath;
use App\Services\Manage\Report\ChallengePathReportService;
use App\Services\Manage\Report\ChallengeReportService;

class ChallengeReportRepository implements ChallengeReportInterface
{
    public function __construct(
        protected ChallengeReportService $challengeReportService,
        protected ChallengePathReportService $challengePathReportService
    ) {
    }

    public function getChallengeMemberProgress(Challenge $challenge): false|array
    {
        try {
            return $this->challengeReportService->getChallengeMemberProgress($challenge);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getChallengeEngagement(Challenge|null $challenge): false|array
    {
        try {
            return $this->challengeReportService->getChallengeEngagement($challenge);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedLabs(Challenge $challenge): array|false
    {
        try {
            return $this->challengeReportService->getPaginatedLabs($challenge);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedLabPrograms(Challenge $challenge): array|false
    {
        try {
            return $this->challengeReportService->getPaginatedLabPrograms($challenge);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedResourceModules(Challenge $challenge): array|false
    {
        try {
            return $this->challengeReportService->getPaginatedResourceModules($challenge);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedResourceCollections(Challenge $challenge): false|array
    {
        try {
            return $this->challengeReportService->getPaginatedResourceCollections($challenge);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedResourceGroups(Challenge $challenge): false|array
    {
        try {
            return $this->challengeReportService->getPaginatedResourceGroups($challenge);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedAchievements(Challenge $challenge): false|array
    {
        try {
            return $this->challengeReportService->getPaginatedAchievements($challenge);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedMembers(Challenge $challenge): false|array
    {
        try {
            return $this->challengeReportService->getPaginatedMembers($challenge);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function challengeMemberActivity(Challenge $challenge): array|false
    {
        try {
            return $this->challengeReportService->challengeMemberActivity($challenge);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedAssessments(Challenge $challenge): false|array
    {
        try {
            return $this->challengeReportService->getPaginatedAssessments($challenge);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getChallengeAssessmentDetail(Challenge $challenge, $project_id): false|array
    {
        try {
            return $this->challengeReportService->getChallengeAssessmentDetail($challenge, $project_id);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getChallengeAssociatedProjects(Challenge $challenge): false|array
    {
        try {
            return $this->challengeReportService->getChallengeAssociatedProjects($challenge);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getChallengePathMemberProgress(ChallengePath $challengePath): false|array
    {
        try {
            return $this->challengePathReportService->getChallengePathMemberProgress($challengePath);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
