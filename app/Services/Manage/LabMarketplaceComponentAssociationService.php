<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use App\Models\ChallengePath;
use App\Models\ChallengePathTemplate;
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
                    $checkChallengeTemplate = $this->challengeTemplateRepository->getCheckChallengeUuid($getChallenges->uuid);
                    if (!$checkChallengeTemplate) {
                        $challengesTemplate = $this->challengeTemplateRepository->addChallengeToTemplate($getChallenges->id);
                        self::createLabMarkeplaceChallenge($labMarketplaceId, $challengesTemplate->id, $componentAssociation->sequence);
                    }
                }

                if ($componentAssociation->challenge_path_id !== null) {
                    $getChallengePaths = ChallengePath::where('id', $componentAssociation->challenge_path_id)->first();
                    $challengesPathTemplate = $this->challengePathTemplateRepository->addChallengePathToTemplate($getChallengePaths->id);
                    self::createLabMarkeplaceChallengePath($labMarketplaceId, $challengesPathTemplate->id, $componentAssociation->sequence);
                }

                if ($componentAssociation->resource_module_id !== null) {
                    self::createLabMarkeplaceModule($labMarketplaceId, $componentAssociation->resource_module_id, $componentAssociation->sequence);
                }

                if ($componentAssociation->resource_collection_id !== null) {
                    self::createLabMarkeplaceCollection($labMarketplaceId, $componentAssociation->resource_collection_id, $componentAssociation->sequence);
                }

                if ($componentAssociation->resource_group_id !== null) {
                    self::createLabMarkeplaceGroup($labMarketplaceId, $componentAssociation->resource_group_id, $componentAssociation->sequence);
                }
            }

            return true;
        } catch(Exception $e) {
            UtilityHelper::logError($e);

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
                            $challengeTemplate = $this->challengeTemplateRepository->challengeRedeem($getChallenge->id, $organizationId);
                            self::createRedeemChallenge($redeemLabId, $challengeTemplate->id, $labMarketplaceComponentAssociation->sequence);
                        }
                    }

                    if ($labMarketplaceComponentAssociation->challenge_path_template_id !== null) {
                        $getChallengePathTemplate = ChallengePathTemplate::where('id', $labMarketplaceComponentAssociation->challenge_path_template_id)->first();
                        if ($getChallengePathTemplate) {
                            $challengePathTemplate = $this->challengePathTemplateRepository->redeemChallengePath($getChallengePathTemplate->id, $organizationId);
                            self::createRedeemChallengePath($redeemLabId, $challengePathTemplate->id, $labMarketplaceComponentAssociation->sequence);
                        }
                    }

                    if ($labMarketplaceComponentAssociation->resource_module_id !== null) {
                        self::createRedeemResourceModule($redeemLabId, $labMarketplaceComponentAssociation->resource_module_id, $labMarketplaceComponentAssociation->sequence);
                    }

                    if ($labMarketplaceComponentAssociation->resource_collection_id !== null) {
                        self::createRedeemResourceCollection($redeemLabId, $labMarketplaceComponentAssociation->resource_collection_id, $labMarketplaceComponentAssociation->sequence);
                    }

                    if ($labMarketplaceComponentAssociation->resource_group_id !== null) {
                        self::createRedeemResourceGroup($redeemLabId, $labMarketplaceComponentAssociation->resource_group_id, $labMarketplaceComponentAssociation->sequence);
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createRedeemChallenge($labRedeemId, $challengeRedeemId, $sequenceNumber)
    {
        try {
            $challengeRedeem = new ComponentAssociation();
            $challengeRedeem->lab_id = $labRedeemId;
            $challengeRedeem->challenge_id = $challengeRedeemId;
            $challengeRedeem->sequence = $sequenceNumber;
            $challengeRedeem->save();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createRedeemChallengePath($labRedeemId, $challengePathRedeemId, $sequenceNumber)
    {
        try {
            $challengeRedeem = new ComponentAssociation();
            $challengeRedeem->lab_id = $labRedeemId;
            $challengeRedeem->challenge_path_id = $challengePathRedeemId;
            $challengeRedeem->sequence = $sequenceNumber;
            $challengeRedeem->save();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createRedeemResourceModule($labRedeemId, $resourceModuleId, $sequenceNumber)
    {
        try {
            $challengeRedeem = new ComponentAssociation();
            $challengeRedeem->lab_id = $labRedeemId;
            $challengeRedeem->resource_module_id = $resourceModuleId;
            $challengeRedeem->sequence = $sequenceNumber;
            $challengeRedeem->save();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createRedeemResourceCollection($labRedeemId, $resourceCollectionId, $sequenceNumber)
    {
        try {
            $challengeRedeem = new ComponentAssociation();
            $challengeRedeem->lab_id = $labRedeemId;
            $challengeRedeem->resource_collection_id = $resourceCollectionId;
            $challengeRedeem->sequence = $sequenceNumber;
            $challengeRedeem->save();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createRedeemResourceGroup($labRedeemId, $resourceGroupId, $sequenceNumber)
    {
        try {
            $challengeRedeem = new ComponentAssociation();
            $challengeRedeem->lab_id = $labRedeemId;
            $challengeRedeem->resource_group_id = $resourceGroupId;
            $challengeRedeem->sequence = $sequenceNumber;
            $challengeRedeem->save();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createLabMarkeplaceChallenge($labMarketplaceId, $challengeTemplateId, $sequenceNumber)
    {
        try {
            $labMarketPlaceChallenge = new LabMarketplaceComponentAssociations();
            $labMarketPlaceChallenge->lab_marketplace_id = $labMarketplaceId;
            $labMarketPlaceChallenge->challenge_template_id = $challengeTemplateId;
            $labMarketPlaceChallenge->sequence = $sequenceNumber;
            $labMarketPlaceChallenge->save();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createLabMarkeplaceModule($labMarketplaceId, $resourceModuleId, $sequenceNumber)
    {
        try {
            $labMarketPlaceModule = new LabMarketplaceComponentAssociations();
            $labMarketPlaceModule->lab_marketplace_id = $labMarketplaceId;
            $labMarketPlaceModule->resource_module_id = $resourceModuleId;
            $labMarketPlaceModule->sequence = $sequenceNumber;
            $labMarketPlaceModule->save();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createLabMarkeplaceCollection($labMarketplaceId, $resourceCollectionId, $sequenceNumber)
    {
        try {
            $labMarketPlaceCollection = new LabMarketplaceComponentAssociations();
            $labMarketPlaceCollection->lab_marketplace_id = $labMarketplaceId;
            $labMarketPlaceCollection->resource_collection_id = $resourceCollectionId;
            $labMarketPlaceCollection->sequence = $sequenceNumber;
            $labMarketPlaceCollection->save();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createLabMarkeplaceGroup($labMarketplaceId, $resourceGroupId, $sequenceNumber)
    {
        try {
            $labMarketPlaceGroup = new LabMarketplaceComponentAssociations();
            $labMarketPlaceGroup->lab_marketplace_id = $labMarketplaceId;
            $labMarketPlaceGroup->resource_group_id = $resourceGroupId;
            $labMarketPlaceGroup->sequence = $sequenceNumber;
            $labMarketPlaceGroup->save();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createLabMarkeplaceChallengePath($labMarketplaceId, $challengePathTemplateId, $sequenceNumber)
    {
        try {
            $labMarketPlaceChallengePath = new LabMarketplaceComponentAssociations();
            $labMarketPlaceChallengePath->lab_marketplace_id = $labMarketplaceId;
            $labMarketPlaceChallengePath->challenge_path_template_id = $challengePathTemplateId;
            $labMarketPlaceChallengePath->sequence = $sequenceNumber;
            $labMarketPlaceChallengePath->save();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

            return false;
        }
    }
}
