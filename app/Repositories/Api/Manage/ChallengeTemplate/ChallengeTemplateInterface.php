<?php

namespace App\Repositories\Api\Manage\ChallengeTemplate;

interface ChallengeTemplateInterface
{
    public function getChallengeTemplateList($request);

    public function getCheckChallengeUuid($uuid);

    public function addChallengeToTemplate($challengeId);

    public function getChallengeTemplateBasedOnSlug($slug);

    public function addChallengeRedeemData($challengeId, $organizationId, $challengeTemplateId);

    public function checkChallengeRedeemedOrNot($challengeTemplateId, $organizationId);

    public function challengeRedeem($challengeTemplateId, $organizationId);

    public function addChallengeRedeemed($challengeTemplateId, $organizationId, $challengeId);

    public function deleteChallengeTemplate($slug, $challengeTemplateId);
}
