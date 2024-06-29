<?php

namespace App\Repositories\Api\Manage\LabMarketplace;

use App\Helpers\UtilityHelper;
use App\Models\LabChallengeRedeem;
use App\Services\Manage\LabMarketplaceAchievementsService;
use App\Services\Manage\LabMarketplaceAddressService;
use App\Services\Manage\LabMarketplaceComponentAssociationService;
use App\Services\Manage\LabMarketplaceExternalLinksService;
use App\Services\Manage\LabMarketplaceService;
use App\Services\Manage\LabMarketplaceSkillsGroupStackService;
use App\Services\Manage\LabMarketplaceTagsGroupService;
use App\Services\Manage\LabService;
use App\Services\Manage\OrganizationService;
use DB;
use Exception;

class LabMarketplaceRepository implements LabMarketplaceInterface
{
    private $labMarketplaceService;

    private $labService;

    private $labMarketplaceAddressService;

    private $labMarketplaceSkillsGroupStackService;

    private $labMarketplaceTagsGroupsService;

    private $labMarketplaceExternalLinksService;

    private $labMarketplaceAchievementsService;

    private $labMarketplaceComponentAssociationService;

    private $organizationService;

    public function __construct(OrganizationService $organizationService, LabMarketplaceComponentAssociationService $labMarketplaceComponentAssociationService, LabMarketplaceAchievementsService $labMarketplaceAchievementsService, LabMarketplaceExternalLinksService $labMarketplaceExternalLinksService, LabMarketplaceTagsGroupService $labMarketplaceTagsGroupsService, LabMarketplaceSkillsGroupStackService $labMarketplaceSkillsGroupStackService, LabMarketplaceService $labMarketplaceService, LabService $labService, LabMarketplaceAddressService $labMarketplaceAddressService)
    {
        $this->labMarketplaceService = $labMarketplaceService;
        $this->labService = $labService;
        $this->labMarketplaceAddressService = $labMarketplaceAddressService;
        $this->labMarketplaceSkillsGroupStackService = $labMarketplaceSkillsGroupStackService;
        $this->labMarketplaceTagsGroupsService = $labMarketplaceTagsGroupsService;
        $this->labMarketplaceExternalLinksService = $labMarketplaceExternalLinksService;
        $this->labMarketplaceAchievementsService = $labMarketplaceAchievementsService;
        $this->labMarketplaceComponentAssociationService = $labMarketplaceComponentAssociationService;
        $this->organizationService = $organizationService;
    }

    public function getLabMarketPlaceList($request)
    {
        try {
            return $this->labMarketplaceService->getLabMarketPlaceList($request);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getLabBasedOnSlug($slug)
    {
        try {
            return $this->labService->getLabBasedOnSlug($slug);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getCheckLabUuid($uuid)
    {
        try {
            return $this->labMarketplaceService->getCheckLabUuid($uuid);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getOrganizationIdBasedOnUuid($uuid)
    {
        try {
            return $this->organizationService->getOrganizationExistBasedOnUuid($uuid);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getLabMarketplaceBasedOnSlug($slug)
    {
        try {
            return $this->labMarketplaceService->getLabMarketplaceBasedOnSlug($slug);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function addLabToMarketplace($slug, $labId)
    {
        try {
            $addLabMarketplace = DB::transaction(function () use ($slug, $labId) {
                $addLabToMarketplace = $this->labMarketplaceService->addLabToMarketplace($slug);
                $addLabMarketplaceAddress = $this->labMarketplaceAddressService->addLabMarketplaceAddress($addLabToMarketplace->id, $labId);
                $addLabMarketplaceSkillAssociations = $this->labMarketplaceSkillsGroupStackService->addLabMarketplaceSkillsGroupsStack($addLabToMarketplace->id, $labId);
                $addLabMarketplaceTagAssociations = $this->labMarketplaceTagsGroupsService->addLabMarketplaceTagsGroup($addLabToMarketplace->id, $labId);
                $addLabMarketplaceExternalLinks = $this->labMarketplaceExternalLinksService->addLabMarketplaceExternalLinks($addLabToMarketplace->id, $labId);
                $addLabMarketplaceAchievement = $this->labMarketplaceAchievementsService->addLabMarketplaceAchievements($addLabToMarketplace->id, $labId);
                $addLabMarketplaceAssociations = $this->labMarketplaceComponentAssociationService->addLabMarketplaceComponentAssociation($addLabToMarketplace->id, $labId);
                $updateLab = $this->labService->updatePreBuilt($labId, '1');

                return[
                    'labMarketplace'                            => $addLabToMarketplace,
                    'addLabMarketplaceAddress'                  => $addLabMarketplaceAddress,
                    'addLabMarketplaceSkillAssociations'        => $addLabMarketplaceSkillAssociations,
                    'addLabMarketplaceTagAssociations'          => $addLabMarketplaceTagAssociations,
                    'addLabMarketplaceExternalLinks'            => $addLabMarketplaceExternalLinks,
                    'addLabMarketplaceAchievement'              => $addLabMarketplaceAchievement,
                    'addLabMarketplaceAssociations'             => $addLabMarketplaceAssociations,
                    'updateLab'                                 => $updateLab,
                ];
            });
            if ($addLabMarketplace['labMarketplace'] &&
                $addLabMarketplace['addLabMarketplaceAddress'] &&
                $addLabMarketplace['addLabMarketplaceSkillAssociations'] &&
                $addLabMarketplace['addLabMarketplaceTagAssociations'] &&
                $addLabMarketplace['addLabMarketplaceExternalLinks'] &&
                $addLabMarketplace['addLabMarketplaceAchievement'] &&
                $addLabMarketplace['addLabMarketplaceAssociations'] &&
                $addLabMarketplace['updateLab']) {
                self::addLabRedeemData($labId, $addLabMarketplace['labMarketplace']->organization_id, $addLabMarketplace['labMarketplace']->id);
                DB::commit();

                return $addLabMarketplace['labMarketplace'];
            }
            DB::rollback();

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();

            return false;
        }
    }

    public function deleteLabMarketplace($slug, $labMarketplaceId)
    {
        try {
            return $this->labMarketplaceService->deleteLabMarketplace($slug, $labMarketplaceId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function addLabRedeemData($labId, $organizationId, $labMarketplaceId)
    {
        try {
            $labRedeem = new LabChallengeRedeem();
            $labRedeem->user_id = auth()->user()->id;
            $labRedeem->organization_id = $organizationId;
            $labRedeem->lab_id = $labId;
            $labRedeem->lab_marketplace_id = $labMarketplaceId;
            $labRedeem->challenge_id = null;
            $labRedeem->challenge_template_id = null;
            $labRedeem->is_redeemed = '0';
            $labRedeem->save();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function checkLabRedeemedOrNot($labMarketplaceId, $organizationId)
    {
        try {
            return $this->labMarketplaceService->checkLabRedeemedOrNot($labMarketplaceId, $organizationId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function labRedeem($labMarketplaceId, $organizationId)
    {
        try {
            $redeemLabMarketplace = DB::transaction(function () use ($labMarketplaceId, $organizationId) {
                $redeemLabMarketplaceToLab = $this->labMarketplaceService->redeemLabMarketplaceToLab($labMarketplaceId, $organizationId);
                $redeemLabMarketplaceAddress = $this->labMarketplaceAddressService->redeemLabMarketplaceAddress($redeemLabMarketplaceToLab->id, $labMarketplaceId);
                $redeemLabMarketplaceSkillAssociations = $this->labMarketplaceSkillsGroupStackService->redeemLabMarketplaceSkillsGroupsStack($redeemLabMarketplaceToLab->id, $labMarketplaceId);
                $redeemLabMarketplaceTagsGroup = $this->labMarketplaceTagsGroupsService->redeemLabMarketplaceTagsGroup($redeemLabMarketplaceToLab->id, $labMarketplaceId);
                $redeemLabMarketplaceExternalLinks = $this->labMarketplaceExternalLinksService->redeemLabMarketplaceExternalLinks($redeemLabMarketplaceToLab->id, $labMarketplaceId);
                $redeemLabMarketplaceAchievement = $this->labMarketplaceAchievementsService->redeemLabMarketplaceAchievement($redeemLabMarketplaceToLab->id, $labMarketplaceId);
                $redeemLabMarketplaceComponentAssociation = $this->labMarketplaceComponentAssociationService->redeemLabMarketplaceComponentAssociation($redeemLabMarketplaceToLab->id, $labMarketplaceId, $organizationId);

                return [
                    'redeemLabMarketplaceToLab'                 => $redeemLabMarketplaceToLab,
                    'redeemLabMarketplaceAddress'               => $redeemLabMarketplaceAddress,
                    'redeemLabMarketplaceSkillAssociations'     => $redeemLabMarketplaceSkillAssociations,
                    'redeemLabMarketplaceTagsGroup'             => $redeemLabMarketplaceTagsGroup,
                    'redeemLabMarketplaceExternalLinks'         => $redeemLabMarketplaceExternalLinks,
                    'redeemLabMarketplaceAchievement'           => $redeemLabMarketplaceAchievement,
                    'redeemLabMarketplaceComponentAssociation'  => $redeemLabMarketplaceComponentAssociation,
                ];
            });

            if (
                $redeemLabMarketplace['redeemLabMarketplaceToLab'] &&
                $redeemLabMarketplace['redeemLabMarketplaceAddress'] &&
                $redeemLabMarketplace['redeemLabMarketplaceSkillAssociations'] &&
                $redeemLabMarketplace['redeemLabMarketplaceTagsGroup'] &&
                $redeemLabMarketplace['redeemLabMarketplaceExternalLinks'] &&
                $redeemLabMarketplace['redeemLabMarketplaceAchievement'] &&
                $redeemLabMarketplace['redeemLabMarketplaceComponentAssociation']) {
                self::addLabRedeemed($labMarketplaceId, $redeemLabMarketplace['redeemLabMarketplaceToLab']->organization_id, $redeemLabMarketplace['redeemLabMarketplaceToLab']->id);
                DB::commit();

                return $redeemLabMarketplace['redeemLabMarketplaceToLab'];
            }

            DB::rollback();

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();

            return false;
        }
    }

    public function addLabRedeemed($labMarketplaceId, $organizationId, $labId)
    {
        try {
            $labRedeem = new LabChallengeRedeem();
            $labRedeem->user_id = auth()->user()->id;
            $labRedeem->organization_id = $organizationId;
            $labRedeem->lab_id = $labId;
            $labRedeem->lab_marketplace_id = $labMarketplaceId;
            $labRedeem->challenge_id = null;
            $labRedeem->challenge_template_id = null;
            $labRedeem->is_redeemed = '1';
            $labRedeem->save();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
