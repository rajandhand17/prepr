<?php

namespace App\Traits\Maestro\Challenge;

use App\Services\Maestro\ChallengeService;
use App\Services\Maestro\ChallengeTimelineService;
use App\Services\Maestro\ChallengeSkillsGroupsStackService;
use App\Services\Maestro\ChallengeRequirementService;
use App\Services\Maestro\ComponentAssociationService;
use App\Services\Maestro\ChallengeAchievementService;
use Illuminate\Support\Facades\DB;
use Exception;

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
            return false;
        }
    }

    private function createChallenge($request)
    {
        try {
            $createChallenge = DB::transaction(function () use ($request) {
                $challenge      = ChallengeService::createChallenge($request);
                $requirement    = ChallengeRequirementService::challengeRequirementsSave($request, $challenge);
                $timeline       = ChallengeTimelineService::challengeTimelinesSave($request, $challenge);
                $skill_group    = ChallengeSkillsGroupsStackService::challengeSkillsGroupsStacks($request, $challenge);
                $labs           = ComponentAssociationService::addAssociatedLabWithChallenge($request, $challenge);
                $resource_module= ComponentAssociationService::addAssociatedResourceModuleWithChallenge($request, $challenge);
                $incentives     = ChallengeAchievementService::challengeIncentives($request, $challenge);

                return [
                    'challenge'     => $challenge,
                    'requirement'   => $requirement,
                    'timeline'      => $timeline,
                    'skill_group'   => $skill_group,
                    'labs'          => $labs,
                    'resource_module'=> $resource_module,
                    'incentives'    => $incentives,
                ];
            });

            if ($createChallenge['challenge'] && $createChallenge['requirement'] && $createChallenge['timeline'] && $createChallenge['skill_group'] && $createChallenge['labs'] && $createChallenge['resource_module'] && $createChallenge['incentives']) {
                DB::commit();
                return $createChallenge['challenge'];
            }
            DB::rollBack();
            return false;
        } catch (Exception $e) {
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
            return false;
        }
    }

    private function getChallengeById($id)
    {
        try {
            return ChallengeService::getChallengeById($id);
        } catch (Exception $e) {
            return false;
        }
    }

    private function updateChallengeById($id, $request)
    {
        try {
            if (ChallengeService::updateChallengeById($id, $request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getAssessment($challengeId)
    {
        try {
            $assessment = ChallengeService::getAssessment($challengeId);
            if ($assessment) {
                return $assessment;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getCriteria($challengeId)
    {
        try {
            $criteria = ChallengeService::getCriteria($challengeId);
            if ($criteria) {
                return $criteria;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function storeUpdateAssessment($request)
    {
        try {
            $assessment = ChallengeService::storeUpdateAssessment($request);
            if ($assessment) {
                return $assessment;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
