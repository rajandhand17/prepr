<?php

namespace App\Repositories\Api\Manage\ResourceCollection;

use App\Services\Manage\ComponentAssociationService;
use App\Services\Manage\ResourceCollectionService;
use App\Services\Manage\ResourceCollectionSkillsGroupsStackService;
use App\Services\Manage\ResourceCollectionTagsGroupsService;
use DB;

class ResourceCollectionRepository implements ResourceCollectionInterface
{
    private $resourceCollectionService;

    private $componentAssociationService;

    private $resouceCollectionSkillsGroupStackService;

    private $resourceCollectionTagsGroupsService;

    public function __construct(ResourceCollectionService $resourceCollectionService, ComponentAssociationService $componentAssociationService, ResourceCollectionSkillsGroupsStackService $resouceCollectionSkillsGroupStackService, ResourceCollectionTagsGroupsService $resourceCollectionTagsGroupsService)
    {
        $this->resourceCollectionService = $resourceCollectionService;
        $this->componentAssociationService = $componentAssociationService;
        $this->resouceCollectionSkillsGroupStackService = $resouceCollectionSkillsGroupStackService;
        $this->resourceCollectionTagsGroupsService = $resourceCollectionTagsGroupsService;
    }

    public function createResourceCollection($request, $upload_cover_image)
    {
        try {
            $createResourceCollection = DB::transaction(function () use ($request, $upload_cover_image) {
                $createResourceCollection = $this->resourceCollectionService->createResourceCollection($request, $upload_cover_image);
                $componentAssociation = $this->componentAssociationService->createResourceCollectionAssociation($request, $createResourceCollection->id);
                $createResourceCollectionSkillsGroupStack = $this->resouceCollectionSkillsGroupStackService->createResourceCollectionSkillsGroupsStack($request, $createResourceCollection->id);
                $createResourceCollectionTagsGroups = $this->resourceCollectionTagsGroupsService->createCollectionModuleTagsGroups($request, $createResourceCollection->id);

                return[
                    'createResourceCollection'                       => $createResourceCollection,
                    'componentAssociation'                           => $componentAssociation,
                    'createResourceCollectionSkillsGroupStack'       => $createResourceCollectionSkillsGroupStack,
                    'createResourceCollectionTagsGroups'             => $createResourceCollectionTagsGroups,
                ];
            });
            if ($createResourceCollection['createResourceCollection'] && $createResourceCollection['componentAssociation'] && $createResourceCollection['createResourceCollectionSkillsGroupStack'] && $createResourceCollection['createResourceCollectionTagsGroups']) {
                DB::commit();

                return true;
            }
            DB::rollback();

            return false;
        } catch(\Exception $e) {
            DB::rollback();

            return false;
        }
    }

    public function uploadResourceCollectionCoverImage($cover_image)
    {
        try {
            return  $this->resourceCollectionService->uploadResourceCollectionCoverImage($cover_image);
        } catch(\Exception $e) {
            return false;
        }
    }
}
