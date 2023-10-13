<?php

namespace App\Repositories\Api\Manage\ResourceModule;

use App\Services\Manage\ResourceModuleDetailService;
use App\Services\Manage\ResourceModuleRatingService;
use App\Services\Manage\ResourceModuleService;
use App\Services\Manage\ResourceModuleSkillsGroupsStackService;
use App\Services\Manage\ResourceModuleTagsGroupsService;
use DB;

class ResourceModuleRepository implements ResourceModuleInterface
{
    protected $resourceModuleService;

    protected $resourceModuleDetailsService;

    protected $resouceModuleSkillsGroupStackService;
    protected $resourceModuleRatingService;

    protected $resourceModuleTagsGroupsService;

    public function __construct(ResourceModuleService $resourceModuleService, ResourceModuleDetailService $resourceModuleDetailsService, ResourceModuleSkillsGroupsStackService $resouceModuleSkillsGroupStackService, ResourceModuleRatingService $resourceModuleRatingService, ResourceModuleTagsGroupsService $resourceModuleTagsGroupsService)
    {
        $this->resourceModuleService = $resourceModuleService;
        $this->resourceModuleDetailsService = $resourceModuleDetailsService;
        $this->resouceModuleSkillsGroupStackService = $resouceModuleSkillsGroupStackService;
        $this->resourceModuleTagsGroupsService = $resourceModuleTagsGroupsService;
        $this->resourceModuleRatingService = $resourceModuleRatingService;
    }

    public function getResourceModuleList($request, $organization)
    {
        try {
            return  $this->resourceModuleService->getResourceModuleList($request, $organization);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function createResourceModule($request, $upload_cover_image)
    {
        try {
            $createLabProgram = DB::transaction(function () use ($request, $upload_cover_image) {
                $createResourceModule = $this->resourceModuleService->createResourceModule($request, $upload_cover_image);
                $resourceModuleSkillsGroupStackService = $this->resouceModuleSkillsGroupStackService->createResourceModuleSkillsGroupsStack($request, $createResourceModule->id);
                $resourceModuleTagsGroupsService = $this->resourceModuleTagsGroupsService->createResourceModuleTagsGroups($request, $createResourceModule->id);

                return [
                    'createResourceModule'                 => $createResourceModule,
                    'resourceModuleSkillsGroupStackService'=> $resourceModuleSkillsGroupStackService,
                    'resourceModuleTagsGroupsService'      => $resourceModuleTagsGroupsService,
                ];
            });
            if ($createLabProgram['createResourceModule'] && $createLabProgram['resourceModuleSkillsGroupStackService'] && $createLabProgram['resourceModuleTagsGroupsService']) {
                DB::commit();

                return true;
            }
            DB::rollback();

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function uploadResourceModuleCoverImage($cover_image)
    {
        try {
            return $this->resourceModuleService->uploadResourceModuleCoverImage($cover_image);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getResourceModuleBasedOnSlug($slug)
    {
        try {
            return $this->resourceModuleService->getResourceModuleBasedOnSlug($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteResourceModule($slug, $resource_module_id)
    {
        try {
            DB::beginTransaction();
            $deleteResourceModule = $this->resourceModuleService->deleteResourceModule($resource_module_id);
            if ($deleteResourceModule == false) {
                DB::rollBack();

                return false;
            }
            DB::commit();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkName($title)
    {
        try {
            return $this->resourceModuleService->checkName($title);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function updateResourceModule($slug, $request, $upload_cover_image)
    {
        try {
            $updateResourceModule = DB::transaction(function () use ($slug, $request, $upload_cover_image) {
                $updateResourceModule = $this->resourceModuleService->updateResourceModule($slug, $request, $upload_cover_image);
                $resourceModuleSkillsGroupStackService = $this->resouceModuleSkillsGroupStackService->updateResourceModuleSkillsGroupsStack($request, $updateResourceModule->id);
                $resourceModuleTagsGroupsService = $this->resourceModuleTagsGroupsService->updateResourceModuleTagsGroups($request, $updateResourceModule->id);

                return [
                    'updateResourceModule'           => $updateResourceModule,
                    'resourceModuleSkillsGroupsStack'=> $resourceModuleSkillsGroupStackService,
                    'resourceModuleTagsGroupsService'=> $resourceModuleTagsGroupsService,
                ];
            });
            if ($updateResourceModule['updateResourceModule'] && $updateResourceModule['resourceModuleSkillsGroupsStack'] && $updateResourceModule['resourceModuleTagsGroupsService']) {
                DB::commit();

                return true;
            }
            DB::rollback();

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function fileUpload($request, $resource_module_id, $type)
    {
        try {
            return $this->resourceModuleDetailsService->fileUpload($request, $resource_module_id, $type);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function deleteResourceModuleMedia($request, $resource_module_id, $type)
    {
        try {
            return $this->resourceModuleDetailsService->deleteResourceModuleMedia($request, $resource_module_id, $type);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function addLinks($request, $resource_module_id)
    {
        try {
            return $this->resourceModuleDetailsService->addLinks($request, $resource_module_id);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function addEmbeddedMedia($request, $resource_module_id)
    {
        try {
            return $this->resourceModuleDetailsService->addEmbeddedMedia($request, $resource_module_id);
        } catch(\Exception $e) {
            return false;
        }
    }
}
