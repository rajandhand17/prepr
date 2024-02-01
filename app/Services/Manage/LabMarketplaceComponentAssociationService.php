<?php

namespace App\Services\Manage;

use App\Models\Challenge;
use App\Models\ChallengePath;
use App\Models\ChallengeTemplate;
use App\Models\ComponentAssociation;
use App\Models\LabMarketplaceComponentAssociations;
use App\Repositories\Api\Manage\ChallengePathTemplate\ChallengePathTemplateRepository;
use App\Repositories\Api\Manage\ChallengeTemplate\ChallengeTemplateRepository;
use Exception;

class LabMarketplaceComponentAssociationService
{
    private $challengeTemplateRepository;
    private $challengePathTemplateRepository;

    public function __construct(ChallengeTemplateRepository $challengeTemplateRepository, ChallengePathTemplateRepository $challengePathTemplateRepository)
    {
        $this->challengeTemplateRepository = $challengeTemplateRepository;
        $this->challengePathTemplateRepository = $challengePathTemplateRepository;
    }

    public function addLabMarketplaceComponentAssociation($labMarketplaceId, $labId)
    {
        try {
            $componentAssociations = ComponentAssociation::where('lab_id', $labId)->get();
            foreach ($componentAssociations as $componentAssociation) {
                $sequenceNumber = $componentAssociation->sequence;
                if ($componentAssociation->challenge_id !== null) {
                    $getChallenges = Challenge::where('id', $componentAssociation->challenge_id)->first();
                    $challengesTemplate = $this->challengeTemplateRepository->addChallengeToTemplate($getChallenges->id);
                    self::createLabMarkeplaceChallenge($labMarketplaceId, $challengesTemplate->id, $sequenceNumber);
                }

                if ($componentAssociation->challenge_path_id !== null) {
                    $getChallengePaths = ChallengePath::where('id', $componentAssociation->challenge_path_id)->first();
                    $challengesPathTemplate = $this->challengePathTemplateRepository->addChallengePathToTemplate($getChallengePaths->id);
                    self::createLabMarkeplaceChallengePath($labMarketplaceId, $challengesPathTemplate->id, $sequenceNumber);
                }
            }

            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    public function redeemLabMarketplaceComponentAssociation($redeemLabId, $labMarketplaceId, $organizationId)
    {
        try {
            $labMarketplaceComponentAssociationData = LabMarketplaceComponentAssociations::where('lab_marketplace_id', $labMarketplaceId)->get();
            if (!empty($labMarketplaceComponentAssociationData)) {
                foreach ($labMarketplaceComponentAssociationData as $labMarketplaceComponentAssociation) {
                    $getChallenge = ChallengeTemplate::where('id', $labMarketplaceComponentAssociation->challenge_template_id)->first();
                    if ($getChallenge) {
                        $challengeTemplate = $this->challengeTemplateRepository->addChallengeToTemplate($getChallenge->id, $organizationId);
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createLabMarkeplaceChallenge($labMarketplaceId, $challengeTemplateId, $sequenceNumber)
    {
        try {
            $labSkillsGroupsStack = new LabMarketplaceComponentAssociations();
            $labSkillsGroupsStack->lab_marketplace_id = $labMarketplaceId;
            $labSkillsGroupsStack->lab_program_id = null;
            $labSkillsGroupsStack->challenge_template_id = $challengeTemplateId;
            $labSkillsGroupsStack->challenge_path_template_id = null;
            $labSkillsGroupsStack->resource_module_id = null;
            $labSkillsGroupsStack->resource_collection_id = null;
            $labSkillsGroupsStack->resource_group_id = null;
            $labSkillsGroupsStack->sequence = $sequenceNumber;
            $labSkillsGroupsStack->save();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createLabMarkeplaceChallengePath($labMarketplaceId, $challengePathTemplateId, $sequenceNumber)
    {
        try {
            $labSkillsGroupsStack = new LabMarketplaceComponentAssociations();
            $labSkillsGroupsStack->lab_marketplace_id = $labMarketplaceId;
            $labSkillsGroupsStack->lab_program_id = null;
            $labSkillsGroupsStack->challenge_template_id = null;
            $labSkillsGroupsStack->challenge_path_template_id = $challengePathTemplateId;
            $labSkillsGroupsStack->resource_module_id = null;
            $labSkillsGroupsStack->resource_collection_id = null;
            $labSkillsGroupsStack->resource_group_id = null;
            $labSkillsGroupsStack->sequence = $sequenceNumber;
            $labSkillsGroupsStack->save();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteLabMarketplaceComponentAssociation($labMarketplaceId)
    {
        try {
            $labMarketplaceComponentAssociations = LabMarketplaceComponentAssociations::where('lab_marketplace_id', $labMarketplaceId)->first();
            if ($labMarketplaceComponentAssociations) {
                $deleteLabMarketplaceComponentAssociation = LabMarketplaceComponentAssociations::where('lab_marketplace_id', $labMarketplaceId)->delete();
                if (!$deleteLabMarketplaceComponentAssociation) {
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
