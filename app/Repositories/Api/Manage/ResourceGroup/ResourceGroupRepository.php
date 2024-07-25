<?php

namespace App\Repositories\Api\Manage\ResourceGroup;

use App\Helpers\UtilityHelper;
use App\Services\Manage\ComponentAssociationService;
use App\Services\Manage\ResourceGroupAchievementService;
use App\Services\Manage\ResourceGroupService;
use App\Services\Manage\ResourceGroupSkillsGroupsStackService;
use App\Services\Manage\ResourceGroupTagsGroupsService;
use DB;
use Exception;

class ResourceGroupRepository implements ResourceGroupInterface
{
    private $resourceGroupService;

    private $componentAssociationService;
    private $resourceGroupSkillsGroupStackService;

    private $resourceGroupTagsGroupService;

    private $resourceGroupAchievementsService;

    public function __construct(ResourceGroupService $resourceGroupService, ComponentAssociationService $componentAssociationService, ResourceGroupSkillsGroupsStackService $resourceGroupSkillsGroupStackService, ResourceGroupTagsGroupsService $resourceGroupTagsGroupService, ResourceGroupAchievementService $resourceGroupAchievementsService)
    {
        $this->resourceGroupService = $resourceGroupService;
        $this->componentAssociationService = $componentAssociationService;
        $this->resourceGroupSkillsGroupStackService = $resourceGroupSkillsGroupStackService;
        $this->resourceGroupTagsGroupService = $resourceGroupTagsGroupService;
        $this->resourceGroupAchievementsService = $resourceGroupAchievementsService;
    }

    public function getResourceGroupCountBasedOnOrganization($organizationId)
    {
        try {
            $this->resourceGroupService->getResourceGroupCountBasedOnOrganization($organizationId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createResourceGroup($request, $upload_cover_image, $upload_achievement_image, $organizationId)
    {
        try {
            $createResourceGroup = DB::transaction(function () use ($request, $upload_cover_image, $upload_achievement_image, $organizationId) {
                $createResourceGroup = $this->resourceGroupService->createResourceGroup($request, $upload_cover_image, $organizationId);
                $createResourceGroupComponentAssociation = $this->componentAssociationService->createResourceGroupComponentAssociation($request, $createResourceGroup->id);
                $createResourceGroupSkillsGroupStack = $this->resourceGroupSkillsGroupStackService->createResourceGroupSkillsGroupsStack($request, $createResourceGroup->id);
                $createResourceGroupsAchievements = $this->resourceGroupAchievementsService->createResourceGroupsAchievements($request, $upload_achievement_image, $createResourceGroup->id);

                return[
                    'createResourceGroup'                             => $createResourceGroup,
                    'createResourceGroupComponentAssociation'         => $createResourceGroupComponentAssociation,
                    'createResourceGroupSkillsGroupStack'             => $createResourceGroupSkillsGroupStack,
                    'createResourceGroupsAchievements'                => $createResourceGroupsAchievements,
                ];
            });
            if ($createResourceGroup['createResourceGroup']) {
                DB::commit();

                return $createResourceGroup['createResourceGroup'];
            }
            DB::rollback();

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();

            return false;
        }
    }

    public function uploadResourceGroupCoverImage($cover_image)
    {
        try {
            return  $this->resourceGroupService->uploadResourceGroupCoverImage($cover_image);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function uploadAchievementImage($achievement_image)
    {
        try {
            return  $this->resourceGroupAchievementsService->uploadAchievementImage($achievement_image);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getResourceGroupBasedOnSlug($slug)
    {
        try {
            return  $this->resourceGroupService->getResourceGroupBasedOnSlug($slug);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getResourceGroupBasedOnTitle($title)
    {
        try {
            return $this->resourceGroupService->getResourceGroupBasedOnTitle($title);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function deleteGroupModule($checkResourceGroupId)
    {
        try {
            DB::beginTransaction();
            $deleteResourceGroup = $this->resourceGroupService->deleteGroupModule($checkResourceGroupId);
            if ($deleteResourceGroup == false) {
                DB::rollBack();

                return false;
            }
            DB::commit();

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkName($slug)
    {
        try {
            return  $this->resourceGroupService->checkName($slug);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateResourceGroup($slug, $request, $upload_cover_image, $upload_achievement_image, $organizationId)
    {
        try {
            $updateResourceGroup = DB::transaction(function () use ($slug, $request, $upload_cover_image, $upload_achievement_image, $organizationId) {
                $updateResourceGroup = $this->resourceGroupService->updateResourceGroup($slug, $request, $upload_cover_image, $organizationId);
                $updateResourceGroupComponentAssociation = $this->componentAssociationService->updateResourceGroupComponentAssociation($request, $updateResourceGroup->id);
                $updateResourceGroupSkillsGroupStack = $this->resourceGroupSkillsGroupStackService->updateResourceGroupSkillsGroupsStack($request, $updateResourceGroup->id);
                $updateResourceGroupsAchievements = $this->resourceGroupAchievementsService->updateResourceGroupsAchievements($request, $upload_achievement_image, $updateResourceGroup->id);

                return[
                    'updateResourceGroup'                             => $updateResourceGroup,
                    'updateResourceGroupComponentAssociation'         => $updateResourceGroupComponentAssociation,
                    'updateResourceGroupSkillsGroupStack'             => $updateResourceGroupSkillsGroupStack,
                    'updateResourceGroupsAchievements'                => $updateResourceGroupsAchievements,
                ];
            });
            if ($updateResourceGroup['updateResourceGroup']) {
                DB::commit();

                return $updateResourceGroup['updateResourceGroup'];
            }
            DB::rollback();

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();

            return false;
        }
    }

    public function getResourceGroupList($request, $organization)
    {
        try {
            return  $this->resourceGroupService->getResourceGroupList($request, $organization);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getResourceGroupListName($request, $organization)
    {
        try {
            return $this->resourceGroupService->getResourceGroupListName($request, $organization);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function cloneResourceGroup($resourceGroupId)
    {
        try {
            $getResourceGroup = $this->resourceGroupService->getResourcesWithRelations($resourceGroupId);
            $cloneResourceGroups = DB::transaction(function () use ($getResourceGroup) {
                $cloneResourceGroup = $this->resourceGroupService->cloneResourceGroup($getResourceGroup);
                $cloneResourceGroupComponentAssociation = $this->componentAssociationService->cloneResourceGroupComponentAssociation($cloneResourceGroup->component_association, $getResourceGroup->id);
                $cloneResourceGroupSkillsGroupStack = $this->resourceGroupSkillsGroupStackService->cloneResourceGroupSkillsGroupsStack($cloneResourceGroup->skills_group_stack, $getResourceGroup->id);
                $cloneResourceGroupsAchievements = $this->resourceGroupAchievementsService->cloneResourceGroupsAchievements($cloneResourceGroup->resource_group_achievement, $getResourceGroup->id);

                return[
                    'cloneResourceGroups'                            => $cloneResourceGroup,
                    'cloneResourceGroupComponentAssociation'         => $cloneResourceGroupComponentAssociation,
                    'cloneResourceGroupSkillsGroupStack'             => $cloneResourceGroupSkillsGroupStack,
                    'cloneResourceGroupTagsGroups'                   => $cloneResourceGroupsAchievements,
                ];
            });
            if ($cloneResourceGroups['cloneResourceGroups'] &&
                $cloneResourceGroups['cloneResourceGroupComponentAssociation'] &&
                $cloneResourceGroups['cloneResourceGroupSkillsGroupStack'] &&
                $cloneResourceGroups['cloneResourceGroupTagsGroups']) {
                DB::commit();

                return $cloneResourceGroups['cloneResourceGroups'];
            }
            DB::rollback();

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();

            return false;
        }
    }
}
