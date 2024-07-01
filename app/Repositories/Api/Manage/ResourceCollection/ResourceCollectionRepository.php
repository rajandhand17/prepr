<?php

namespace App\Repositories\Api\Manage\ResourceCollection;

use App\Helpers\UtilityHelper;
use App\Services\Manage\ComponentAssociationService;
use App\Services\Manage\ResourceCollectionService;
use App\Services\Manage\ResourceCollectionSkillsGroupsStackService;
use App\Services\Manage\ResourceCollectionTagsGroupsService;
use DB;
use Exception;

class ResourceCollectionRepository implements ResourceCollectionInterface
{
    private $resourceCollectionService;

    private $componentAssociationService;

    private $resourceCollectionSkillsGroupStackService;

    private $resourceCollectionTagsGroupsService;

    public function __construct(ResourceCollectionService $resourceCollectionService, ComponentAssociationService $componentAssociationService, ResourceCollectionSkillsGroupsStackService $resourceCollectionSkillsGroupStackService, ResourceCollectionTagsGroupsService $resourceCollectionTagsGroupsService)
    {
        $this->resourceCollectionService = $resourceCollectionService;
        $this->componentAssociationService = $componentAssociationService;
        $this->resourceCollectionSkillsGroupStackService = $resourceCollectionSkillsGroupStackService;
        $this->resourceCollectionTagsGroupsService = $resourceCollectionTagsGroupsService;
    }

    public function getResourceCollectionCountBasedOnOrganization($organizationId)
    {
        try {
            return $this->resourceCollectionService->getResourceCollectionCountBasedOnOrganization($organizationId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createResourceCollection($request, $upload_cover_image, $organizationId)
    {
        try {
            $createResourceCollection = DB::transaction(function () use ($request, $upload_cover_image, $organizationId) {
                $createResourceCollection = $this->resourceCollectionService->createResourceCollection($request, $upload_cover_image, $organizationId);
                $createComponentAssociation = $this->componentAssociationService->createResourceCollectionAssociation($request, $createResourceCollection->id);
                $createResourceCollectionSkillsGroupStack = $this->resourceCollectionSkillsGroupStackService->createResourceCollectionSkillsGroupsStack($request, $createResourceCollection->id);
                $createResourceCollectionTagsGroups = $this->resourceCollectionTagsGroupsService->createCollectionModuleTagsGroups($request, $createResourceCollection->id);

                return[
                    'createResourceCollection'                             => $createResourceCollection,
                    'createComponentAssociation'                           => $createComponentAssociation,
                    'createResourceCollectionSkillsGroupStack'             => $createResourceCollectionSkillsGroupStack,
                    'createResourceCollectionTagsGroups'                   => $createResourceCollectionTagsGroups,
                ];
            });
            if ($createResourceCollection['createResourceCollection'] && $createResourceCollection['createComponentAssociation'] && $createResourceCollection['createResourceCollectionSkillsGroupStack'] && $createResourceCollection['createResourceCollectionTagsGroups']) {
                DB::commit();

                return $createResourceCollection['createResourceCollection'];
            }
            DB::rollback();

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();

            return false;
        }
    }

    public function uploadResourceCollectionCoverImage($cover_image)
    {
        try {
            return $this->resourceCollectionService->uploadResourceCollectionCoverImage($cover_image);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getResourceCollectionBasedOnSlug($slug)
    {
        try {
            return $this->resourceCollectionService->getResourceCollectionBasedOnSlug($slug);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkName($title)
    {
        try {
            return $this->resourceCollectionService->checkName($title);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateResourceCollection($slug, $request, $upload_cover_image, $organizationId)
    {
        try {
            $updateResourceCollection = DB::transaction(function () use ($slug, $request, $upload_cover_image, $organizationId) {
                $updateResourceCollection = $this->resourceCollectionService->updateResourceCollection($slug, $request, $upload_cover_image, $organizationId);
                $updateComponentAssociation = $this->componentAssociationService->updateResourceCollectionAssociation($request, $updateResourceCollection->id);
                $updateResourceCollectionSkillsGroupStack = $this->resourceCollectionSkillsGroupStackService->updateResourceCollectionSkillsGroupsStack($request, $updateResourceCollection->id);
                $updateResourceCollectionTagsGroups = $this->resourceCollectionTagsGroupsService->updateCollectionModuleTagsGroups($request, $updateResourceCollection->id);

                return[
                    'updateResourceCollection'                       => $updateResourceCollection,
                    'updateComponentAssociation'                     => $updateComponentAssociation,
                    'updateResourceCollectionSkillsGroupStack'       => $updateResourceCollectionSkillsGroupStack,
                    'updateResourceCollectionTagsGroups'             => $updateResourceCollectionTagsGroups,
                ];
            });
            if ($updateResourceCollection['updateResourceCollection'] && $updateResourceCollection['updateComponentAssociation'] && $updateResourceCollection['updateResourceCollectionSkillsGroupStack'] && $updateResourceCollection['updateResourceCollectionTagsGroups']) {
                DB::commit();

                return $updateResourceCollection['updateResourceCollection'];
            }
            DB::rollback();

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getResourceCollectionList($request, $organization)
    {
        try {
            return $this->resourceCollectionService->getResourceCollectionList($request, $organization);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function deleteResourceCollection($resource_collection_id)
    {
        try {
            DB::beginTransaction();
            $deleteResourceCollection = $this->resourceCollectionService->deleteResourceCollection($resource_collection_id);
            if ($deleteResourceCollection == false) {
                DB::rollBack();

                return false;
            }
            DB::commit();

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return  false;
        }
    }

    public function getListName($request, $organization)
    {
        try {
            return $this->resourceCollectionService->getListName($request, $organization);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
