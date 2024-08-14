<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use App\Models\ChallengePath;
use App\Models\ComponentAssociation;
use App\Models\LabMarketplaceComponentAssociations;
use App\Repositories\Api\Manage\ChallengePathTemplate\ChallengePathTemplateRepository;
use App\Traits\Maestro\ChallengeTemplate\ChallengeTemplateTrait;
use Exception;

class LabMarketplaceComponentAssociationService
{
    private $challengeTemplateRepository;
    protected $challengePathTemplateRepository;

    public function __construct(ChallengeTemplateTrait $challengeTemplateRepository, ChallengePathTemplateRepository $challengePathTemplateRepository)
    {
        $this->challengeTemplateRepository = $challengeTemplateRepository;
        $this->challengePathTemplateRepository = $challengePathTemplateRepository;
    }

    public static function addLabMarketplaceComponentAssociation($labMarketplaceId, $labId)
    {
        try {
            $componentAssociations = ComponentAssociation::where('lab_id', $labId)->get();
            foreach ($componentAssociations as $componentAssociation) {
                if ($componentAssociation->challenge_id !== null) {
                    $getChallenges = Challenge::where('id', $componentAssociation->challenge_id)->first();
                    $checkChallengeTemplate = ChallengeTemplateService::getCheckChallengeUuid($getChallenges->uuid);
                    if (!$checkChallengeTemplate) {
                        $challengesTemplate = $this->challengeTemplateRepository->createChallengeTemplate($getChallenges->id);
                        self::createLabMarketplaceChallenge($labMarketplaceId, $challengesTemplate->id, $componentAssociation->sequence);
                    }
                }

                if ($componentAssociation->challenge_path_id !== null) {
                    $getChallengePaths = ChallengePath::where('id', $componentAssociation->challenge_path_id)->first();
                    $challengesPathTemplate = $this->challengePathTemplateRepository->addChallengePathToTemplate($getChallengePaths->id);
                    self::createLabMarketplaceChallengePath($labMarketplaceId, $challengesPathTemplate->id, $componentAssociation->sequence);
                }

                if ($componentAssociation->resource_module_id !== null) {
                    self::createLabMarketplaceModule($labMarketplaceId, $componentAssociation->resource_module_id, $componentAssociation->sequence);
                }

                if ($componentAssociation->resource_collection_id !== null) {
                    self::createLabMarketplaceCollection($labMarketplaceId, $componentAssociation->resource_collection_id, $componentAssociation->sequence);
                }

                if ($componentAssociation->resource_group_id !== null) {
                    self::createLabMarketplaceGroup($labMarketplaceId, $componentAssociation->resource_group_id, $componentAssociation->sequence);
                }
            }

            return true;
        } catch(Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createLabMarketplaceChallenge($labMarketplaceId, $challengeTemplateId, $sequenceNumber)
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

    public static function createLabMarketplaceChallengePath($labMarketplaceId, $challengePathTemplateId, $sequenceNumber)
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

    public static function createLabMarketplaceModule($labMarketplaceId, $resourceModuleId, $sequenceNumber)
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

    public static function createLabMarketplaceCollection($labMarketplaceId, $resourceCollectionId, $sequenceNumber)
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

    public static function createLabMarketplaceGroup($labMarketplaceId, $resourceGroupId, $sequenceNumber)
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
}
