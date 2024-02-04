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
                if ($componentAssociation->challenge_id !== null) {
                    $getChallenges = Challenge::where('id', $componentAssociation->challenge_id)->first();
                    $challengesTemplate = $this->challengeTemplateRepository->addChallengeToTemplate($getChallenges->id);
                    self::createLabMarkeplaceChallenge($labMarketplaceId, $challengesTemplate->id, $componentAssociation->sequence);
                }

                if ($componentAssociation->challenge_path_id !== null) {
                    $getChallengePaths = ChallengePath::where('id', $componentAssociation->challenge_path_id)->first();
                    $challengesPathTemplate = $this->challengePathTemplateRepository->addChallengePathToTemplate($getChallengePaths->id);
                    self::createLabMarkeplaceChallengePath($labMarketplaceId, $challengesPathTemplate->id, $componentAssociation->sequence);
                }

                if ($componentAssociation->resource_module_id !==  null) {
                    self::createLabMarkeplaceModule($labMarketplaceId, $componentAssociation->resource_module_id, $componentAssociation->sequence);
                }

                if ($componentAssociation->resource_collection_id !==  null) {
                    self::createLabMarkeplaceCollection($labMarketplaceId, $componentAssociation->resource_collection_id, $componentAssociation->sequence);
                }

                if ($componentAssociation->resource_group_id !==  null) {
                    self::createLabMarkeplaceGroup($labMarketplaceId, $componentAssociation->resource_group_id, $componentAssociation->sequence);
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
                    if ($labMarketplaceComponentAssociation->challenge_id !== null) {
                        $getChallenge = ChallengeTemplate::where('id', $labMarketplaceComponentAssociation->challenge_template_id)->first();
                        if ($getChallenge) {
                            $challengeTemplate = $this->challengeTemplateRepository->addChallengeToTemplate($getChallenge->id, $organizationId);
                            self::createRedeemChallenge($redeemLabId, $challengeTemplate->id, $labMarketplaceComponentAssociation->sequence);
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createRedeemChallenge($labRedeemId, $challengeRedeemId, $sequenceNumber)
    {
        try {
            $challengeRedeem = new ComponentAssociation();
            $challengeRedeem->lab_id = $labRedeemId;
            $challengeRedeem->lab_program_id = null;
            $challengeRedeem->challenge_id = $challengeRedeemId;
            $challengeRedeem->challenge_path_id = null;
            $challengeRedeem->resource_module_id = null;
            $challengeRedeem->resource_collection_id = null;
            $challengeRedeem->resource_group_id = null;
            $challengeRedeem->sequence = $sequenceNumber;
            $challengeRedeem->save();
        } catch (Exception $e) {
            return false;
        }
    }


    public static function createLabMarkeplaceChallenge($labMarketplaceId, $challengeTemplateId, $sequenceNumber)
    {
        try {
            $labMarketPlaceChallenge = new LabMarketplaceComponentAssociations();
            $labMarketPlaceChallenge->lab_marketplace_id = $labMarketplaceId;
            $labMarketPlaceChallenge->lab_program_id = null;
            $labMarketPlaceChallenge->challenge_template_id = $challengeTemplateId;
            $labMarketPlaceChallenge->challenge_path_template_id = null;
            $labMarketPlaceChallenge->resource_module_id = null;
            $labMarketPlaceChallenge->resource_collection_id = null;
            $labMarketPlaceChallenge->resource_group_id = null;
            $labMarketPlaceChallenge->sequence = $sequenceNumber;
            $labMarketPlaceChallenge->save();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createLabMarkeplaceModule($labMarketplaceId, $resourceModuleId, $sequenceNumber)
    {
        try {
            $labMarketPlaceModule = new LabMarketplaceComponentAssociations();
            $labMarketPlaceModule->lab_marketplace_id = $labMarketplaceId;
            $labMarketPlaceModule->lab_program_id = null;
            $labMarketPlaceModule->challenge_template_id = null;
            $labMarketPlaceModule->challenge_path_template_id = null;
            $labMarketPlaceModule->resource_module_id = $resourceModuleId;
            $labMarketPlaceModule->resource_collection_id = null;
            $labMarketPlaceModule->resource_group_id = null;
            $labMarketPlaceModule->sequence = $sequenceNumber;
            $labMarketPlaceModule->save();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createLabMarkeplaceCollection($labMarketplaceId, $resourceCollectionId, $sequenceNumber)
    {
        try {
            $labMarketPlaceCollection = new LabMarketplaceComponentAssociations();
            $labMarketPlaceCollection->lab_marketplace_id = $labMarketplaceId;
            $labMarketPlaceCollection->lab_program_id = null;
            $labMarketPlaceCollection->challenge_template_id = null;
            $labMarketPlaceCollection->challenge_path_template_id = null;
            $labMarketPlaceCollection->resource_module_id = null;
            $labMarketPlaceCollection->resource_collection_id = $resourceCollectionId;
            $labMarketPlaceCollection->resource_group_id = null;
            $labMarketPlaceCollection->sequence = $sequenceNumber;
            $labMarketPlaceCollection->save();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createLabMarkeplaceGroup($labMarketplaceId, $resourceGroupId, $sequenceNumber)
    {
        try {
            $labMarketPlaceGroup = new LabMarketplaceComponentAssociations();
            $labMarketPlaceGroup->lab_marketplace_id = $labMarketplaceId;
            $labMarketPlaceGroup->lab_program_id = null;
            $labMarketPlaceGroup->challenge_template_id = null;
            $labMarketPlaceGroup->challenge_path_template_id = null;
            $labMarketPlaceGroup->resource_module_id = null;
            $labMarketPlaceGroup->resource_collection_id = null;
            $labMarketPlaceGroup->resource_group_id = $resourceGroupId;
            $labMarketPlaceGroup->sequence = $sequenceNumber;
            $labMarketPlaceGroup->save();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createLabMarkeplaceChallengePath($labMarketplaceId, $challengePathTemplateId, $sequenceNumber)
    {
        try {
            $labMarketPlaceChallengePath = new LabMarketplaceComponentAssociations();
            $labMarketPlaceChallengePath->lab_marketplace_id = $labMarketplaceId;
            $labMarketPlaceChallengePath->lab_program_id = null;
            $labMarketPlaceChallengePath->challenge_template_id = null;
            $labMarketPlaceChallengePath->challenge_path_template_id = $challengePathTemplateId;
            $labMarketPlaceChallengePath->resource_module_id = null;
            $labMarketPlaceChallengePath->resource_collection_id = null;
            $labMarketPlaceChallengePath->resource_group_id = null;
            $labMarketPlaceChallengePath->sequence = $sequenceNumber;
            $labMarketPlaceChallengePath->save();
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
