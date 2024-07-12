<?php

namespace App\Traits\Maestro\ChallengeTemplate;

use App\Helpers\UtilityHelper;
use App\Services\Maestro\ChallengeService;
use App\Services\Maestro\ChallengeTemplateService;

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
            $getChallengeTemplate = $this->challengeTemplateService->getChallengeTemplateBasedOnId($id);
            $challengeService = $this->challengeTemplateService->deleteChallengeTemplate($getChallengeTemplate->slug, $id);
            if ($challengeService) {
                return $challengeService;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getChallengeTemplateById($id)
    {
        try {
            $challenges = $this->challengeTemplateService->getChallengeTemplateBasedOnId($id);
            if ($challenges) {
                return $challenges;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getChallengeBasedOnSlug($slug)
    {
        try {
            return $this->challengeService->getChallengeBasedOnSlug($slug);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getCheckChallengeUuid()
    {
        try {

        }catch (\Exception $e){
            return false;
        }
    }
}
