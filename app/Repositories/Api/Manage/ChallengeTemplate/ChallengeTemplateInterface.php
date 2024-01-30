<?php

namespace App\Repositories\Api\Manage\ChallengeTemplate;

interface ChallengeTemplateInterface
{
    public function getChallengeTemplateList($request);

    public function addChallengeToTemplate($challengeId);

    public function getChallengeTemplateBasedOnSlug($slug);
}
