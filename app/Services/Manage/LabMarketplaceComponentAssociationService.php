<?php

namespace App\Services\Manage;

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
                    $challengesTemplate = $this->challengeTemplateRepository->addChallengeToTemplate($getChallenges->id);
                    self::createLabMarkeplaceChallenge($labMarketplaceId, $challengesTemplate->id, $componentAssociation->sequence);
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

    public function addChallengeTemplateComponentAssociation($challengeId, $templateChallengeId)
    {
        try {
            $getChallengeAssociations = ComponentAssociation::where('challenge_id', $challengeId)->get();
            if ($getChallengeAssociations->isNotEmpty()) {
                foreach ($getChallengeAssociations as $challengeAssociation) {
                    $newChallengeTemplateAssociation = new LabMarketplaceComponentAssociations();
                    $newChallengeTemplateAssociation->challenge_template_id = $templateChallengeId;
                    $newChallengeTemplateAssociation->sequence = $challengeAssociation->sequence;

                    // Commented for temporary current time being
                    // if ($challengeAssociation->lab_id != null) {
                    //     $newChallengeTemplateAssociation->lab_marketplace_id = $challengeAssociation->lab_id;
                    // } elseif ($challengeAssociation->lab_program_id != null) {
                    //     $newChallengeTemplateAssociation->lab_program_id = $challengeAssociation->lab_program_id;
                    // } elseif ($challengeAssociation->resource_module_id != null) {
                    //     $newChallengeTemplateAssociation->resource_module_id = $challengeAssociation->resource_module_id;
                    // } elseif ($challengeAssociation->resource_collection_id != null) {
                    //     $newChallengeTemplateAssociation->resource_collection_id = $challengeAssociation->resource_collection_id;
                    // } elseif ($challengeAssociation->resource_group_id != null) {
                    //     $newChallengeTemplateAssociation->resource_group_id = $challengeAssociation->resource_group_id;
                    // }

                    if ($challengeAssociation->resource_module_id != null) {
                        $newChallengeTemplateAssociation->resource_module_id = $challengeAssociation->resource_module_id;
                    } elseif ($challengeAssociation->resource_collection_id != null) {
                        $newChallengeTemplateAssociation->resource_collection_id = $challengeAssociation->resource_collection_id;
                    }
                    $newChallengeTemplateAssociation->save();
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function redeemChallengeTemplateComponentAssociation($redeemChallengeId, $challengeTemplateId)
    {
        try {
            $checkChallengeTemplateComponentAssociations = LabMarketplaceComponentAssociations::where('challenge_template_id', $challengeTemplateId)->get();
            if ($checkChallengeTemplateComponentAssociations->isNotEmpty()) {
                foreach ($checkChallengeTemplateComponentAssociations as $challengeTemplateComponentAssociation) {
                    $newChallengeAssociation = new ComponentAssociation();
                    $newChallengeAssociation->challenge_id = $redeemChallengeId;
                    $newChallengeAssociation->sequence = $challengeTemplateComponentAssociation->sequence;

                    // Commented for temporary current time being
                    // if ($challengeTemplateComponentAssociation->lab_marketplace_id != null) {
                    //     $newChallengeAssociation->lab_id = $challengeTemplateComponentAssociation->lab_marketplace_id;
                    // } elseif ($challengeTemplateComponentAssociation->lab_program_id != null) {
                    //     $newChallengeAssociation->lab_program_id = $challengeTemplateComponentAssociation->lab_program_id;
                    // } elseif ($challengeTemplateComponentAssociation->resource_module_id != null) {
                    //     $newChallengeAssociation->resource_module_id = $challengeTemplateComponentAssociation->resource_module_id;
                    // } elseif ($challengeTemplateComponentAssociation->resource_collection_id != null) {
                    //     $newChallengeAssociation->resource_collection_id = $challengeTemplateComponentAssociation->resource_collection_id;
                    // } elseif ($challengeTemplateComponentAssociation->resource_group_id != null) {
                    //     $newChallengeAssociation->resource_group_id = $challengeTemplateComponentAssociation->resource_group_id;
                    // }

                    if ($challengeTemplateComponentAssociation->resource_module_id != null) {
                        $newChallengeAssociation->resource_module_id = $challengeTemplateComponentAssociation->resource_module_id;
                    } elseif ($challengeTemplateComponentAssociation->resource_collection_id != null) {
                        $newChallengeAssociation->resource_collection_id = $challengeTemplateComponentAssociation->resource_collection_id;
                    }
                    $newChallengeAssociation->save();
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
