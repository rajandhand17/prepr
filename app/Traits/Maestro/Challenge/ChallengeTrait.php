<?php


namespace App\Traits\Maestro\Challenge;

use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabMarketplaceService;

trait ChallengeTrait
{
    public function getChallenge()
    {
        try {
            $labMarketplace = ChallengeService::getChallenges();
            if($labMarketplace){
                return $labMarketplace;
            }
            return false;
        }catch (\Exception $e) {
            return false;
        }
    }

    public function deleteChallengeById($id)
    {
        try {
            $challengeService=ChallengeService::deleteChallenge($id);
            if($challengeService){
                return $challengeService;
            }
            return false;
        }catch (\Exception $e){
            return false;
        }
    }

    public function getChallengeById($id)
    {
        try {
        $challenges=ChallengeService::getChallengeBasedOnId($id);
        if($challenges){
            return $challenges;
        }
        return false;
        }catch (\Exception $e){
            return false;
        }
    }
}
