<?php

namespace App\Traits\Maestro\Challenge;

use App\Services\Maestro\ChallengeAchievementService;
use App\Services\Maestro\ChallengeAssessmentCriteriaService;
use App\Services\Maestro\ChallengeAssessmentService;
use App\Services\Maestro\ChallengeRequirementService;
use App\Services\Maestro\ChallengeService;
use App\Services\Maestro\ChallengeSkillsGroupsStackService;
use App\Services\Maestro\ChallengeTimelineService;
use App\Services\Maestro\ComponentAssociationService;
use App\Helpers\UtilityHelper;
use Exception;
use Illuminate\Support\Facades\DB;

trait ChallengeTrait
{
    private function getChallengeList()
    {
        try {
            $challengeList = ChallengeService::getChallengeList();
            if ($challengeList) {
                return $challengeList;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getChallengeAssociatedItemsById($challenge)
    {
        try {
            $associateItems = ChallengeService::getChallengeAssociatedItemsById($challenge);
            if ($associateItems) {
                return $associateItems;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getChallengeIncentives($challenge)
    {
        try {
            $achievements = ChallengeAchievementService::getChallengeIncentives($challenge);
            if ($achievements) {
                return $achievements;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getChallengeTimeLine($challenge)
    {
        try {
            $timelines = ChallengeTimelineService::getChallengeTimeLines($challenge);
            if ($timelines) {
                return $timelines;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function createChallenge($request)
    {
        try {
            $createChallenge = DB::transaction(function () use ($request) {
                $challenge = ChallengeService::createChallenge($request);
                $requirement = ChallengeRequirementService::challengeRequirementsSave($request, $challenge);
                $timeline = ChallengeTimelineService::challengeTimelinesSave($request, $challenge);
                $skill_group = ChallengeSkillsGroupsStackService::challengeSkillsGroupsStacks($request, $challenge);
                $labs = ComponentAssociationService::addAssociatedLabWithChallenge($request, $challenge);
                $resource_module = ComponentAssociationService::addAssociatedResourceModuleWithChallenge($request, $challenge);
                $incentives = ChallengeAchievementService::challengeIncentives($request, $challenge);

                return [
                    'challenge'      => $challenge,
                    'requirement'    => $requirement,
                    'timeline'       => $timeline,
                    'skill_group'    => $skill_group,
                    'labs'           => $labs,
                    'resource_module'=> $resource_module,
                    'incentives'     => $incentives,
                ];
            });

            if ($createChallenge['challenge'] && $createChallenge['requirement'] && $createChallenge['timeline'] && $createChallenge['skill_group'] && $createChallenge['labs'] && $createChallenge['resource_module'] && $createChallenge['incentives']) {
                DB::commit();

                return $createChallenge['challenge'];
            }
            DB::rollBack();

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollBack();

            return false;
        }
    }

    private function deleteChallengeById($id)
    {
        try {
            if (ChallengeService::deleteChallenge($id)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getChallengeById($id)
    {
        try {
            return ChallengeService::getChallengeById($id);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function updateChallengeById($id, $request)
    {
        try {
            $createChallenge = DB::transaction(function () use ($request, $id) {
                $challenge = ChallengeService::updateChallengeById($id, $request);
                $requirement = ChallengeRequirementService::challengeRequirementsSave($request, $challenge);
                $timeline = ChallengeTimelineService::challengeTimelinesSave($request, $challenge);
                $skill_group = ChallengeSkillsGroupsStackService::challengeSkillsGroupsStacks($request, $challenge);
                $labs = ComponentAssociationService::addAssociatedLabWithChallenge($request, $challenge);
                $resource_module = ComponentAssociationService::addAssociatedResourceModuleWithChallenge($request, $challenge);
                $incentives = ChallengeAchievementService::challengeIncentives($request, $challenge);

                return [
                    'challenge'      => $challenge,
                    'requirement'    => $requirement,
                    'timeline'       => $timeline,
                    'skill_group'    => $skill_group,
                    'labs'           => $labs,
                    'resource_module'=> $resource_module,
                    'incentives'     => $incentives,
                ];
            });

            if ($createChallenge['challenge'] && $createChallenge['requirement'] && $createChallenge['timeline'] && $createChallenge['skill_group'] && $createChallenge['labs'] && $createChallenge['resource_module'] && $createChallenge['incentives']) {
                DB::commit();

                return $createChallenge['challenge'];
            }
            DB::rollBack();

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function storeUpdateAssessment($request)
    {
        try {
            $createChallenge = DB::transaction(function () use ($request) {
                $assessmentType = ChallengeAssessmentService::storeUpdateAssessment($request);
                $assessmentCriteria = ChallengeAssessmentCriteriaService::addUpdateAssessmentCriteria($request);

                return [
                    'assessmentType'     => $assessmentType,
                    'assessmentCriteria' => $assessmentCriteria,
                ];
            });

            if ($createChallenge['assessmentType'] && $createChallenge['assessmentCriteria']) {
                DB::commit();

                return $createChallenge['assessmentType'];
            }
            DB::rollBack();

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollBack();

            return false;
        }
    }
}
