<?php

namespace App\Traits\Maestro\ChallengeTemplate;

use App\Services\Manage\ChallengeService;
use App\Services\Manage\ChallengeTemplateService;

trait ChallengeTemplateTrait
{
    public function getChallengeTemplate()
    {
        try {
            $challengeTemplate = $this->challengeTemplateService->getChallengesTemplate();
            if ($challengeTemplate) {
                return $challengeTemplate;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteChallengeTemplateById($id)
    {
        try {
            $getChallengeTemplate = ChallengeTemplateService::getChallengeTemplateBasedOnId($id);
            $challengeService = ChallengeTemplateService::deleteChallengeTemplate($getChallengeTemplate->slug, $id);
            if ($challengeService) {
                return $challengeService;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getChallengeById($id)
    {
        try {
            $challenges = ChallengeService::getChallengeBasedOnId($id);
            if ($challenges) {
                return $challenges;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
