<?php

namespace App\Traits\Maestro\Challenge;

use App\Services\Maestro\Challenge\ChallengeService;
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

    private function getLanguage()
    {
        try {
            $language = ChallengeService::getLanguage();
            if ($language) {
                return $language;
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
            if (ChallengeService::createChallenge($request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
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
}
