<?php

namespace App\Repositories\Api\Manage\ResourceGroup;

use App\Services\Manage\ComponentAssociationService;
use App\Services\Manage\ResourceGroupAchievementService;
use App\Services\Manage\ResourceGroupService;
use App\Services\Manage\ResourceGroupSkillsGroupsStackService;
use App\Services\Manage\ResourceGroupTagsGroupsService;
use DB;

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

    public function createResourceGroup($request, $upload_cover_image, $upload_achievement_image)
    {
        try {
            $createResourceGroup = DB::transaction(function () use ($request, $upload_cover_image, $upload_achievement_image) {
                $createResourceGroup = $this->resourceGroupService->createResourceGroup($request, $upload_cover_image);
                $createResourceGroupComponentAssociation = $this->componentAssociationService->createResourceGroupComponentAssociation($request, $createResourceGroup->id);
                $createResourceGroupSkillsGroupStack = $this->resourceGroupSkillsGroupStackService->createResourceGroupSkillsGroupsStack($request, $createResourceGroup->id);
                $createResourceGroupTagsGroups = $this->resourceGroupTagsGroupService->createResourceGroupTagsGroups($request, $createResourceGroup->id);
                $createResourceGroupsAchievements = $this->resourceGroupAchievementsService->createResourceGroupsAchievements($request, $upload_achievement_image, $createResourceGroup->id);

                return[
                    'createResourceGroup'                             => $createResourceGroup,
                    'createResourceGroupComponentAssociation'         => $createResourceGroupComponentAssociation,
                    'createResourceGroupSkillsGroupStack'             => $createResourceGroupSkillsGroupStack,
                    'createResourceGroupTagsGroups'                   => $createResourceGroupTagsGroups,
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
            DB::rollback();

            return false;
        }
    }

    public function uploadResourceGroupCoverImage($cover_image)
    {
        try {
            return  $this->resourceGroupService->uploadResourceGroupCoverImage($cover_image);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function uploadAchievementImage($achievement_image)
    {
        try {
            return  $this->resourceGroupAchievementsService->uploadAchievementImage($achievement_image);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getResourceGroupBasedOnSlug($slug)
    {
        try {
            return  $this->resourceGroupService->getResourceGroupBasedOnSlug($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteGroupModule($checkResourceGroupId){
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
            return false;
        }
    }
}
