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

    public function __construct(ResourceModuleService $resourceModuleService,ResourceModuleDetailService $resourceModuleDetailsService,ResourceModuleSkillsGroupsStackService $resouceModuleSkillsGroupStackService,ResourceModuleRatingService $resourceModuleRatingService,ResourceModuleTagsGroupsService $resourceModuleTagsGroupsService)
    {
        $this->resourceModuleService = $resourceModuleService;
        $this->resourceModuleDetailsService = $resourceModuleDetailsService;
        $this->resouceModuleSkillsGroupStackService=$resouceModuleSkillsGroupStackService;
        $this->resourceModuleTagsGroupsService=$resourceModuleTagsGroupsService;
        $this->resourceModuleRatingService=$resourceModuleRatingService;
    }

    public function getResourceModuleList($request,$organization)
    {
        try {
            return  $this->resourceModuleService->getResourceModuleList($request,$organization);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function createResourceModule($request,$upload_cover_image)
    {
        try {
            $createLabProgram = DB::transaction(function () use ($request,$upload_cover_image) {
               $createResourceModule=$this->resourceModuleService->createResourceModule($request,$upload_cover_image);
               $resourceModuleSkillsGroupStackService=$this->resouceModuleSkillsGroupStackService->createResourceModuleSkillsGroupsStack($request,$createResourceModule->id);
               $resourceModuleTagsGroupsService=$this->resourceModuleTagsGroupsService->createResourceModuleTagsGroups($request,$createResourceModule->id);
                return [
                    'createResourceModule'           => $createResourceModule,
                    'resourceModuleSkillsGroupStackService'=> $resourceModuleSkillsGroupStackService,
                    'resourceModuleTagsGroupsService'=> $resourceModuleTagsGroupsService,
                ];
            });
            if ($createLabProgram['createResourceModule'] && $createLabProgram['resourceModuleSkillsGroupStackService'] && $createLabProgram['resourceModuleTagsGroupsService']){
               DB::commit();
                return true;
            }
            DB::rollback();
            return false;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function uploadResourceModuleMedia($cover_image){
        try {
            return $this->resourceModuleService->uploadResourceModuleMedia($cover_image);
        }catch(\Exception $e){
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

    public function checkSlug($slug)
    {
        try {
            return $this->resourceModuleService->checkSlug($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete($slug,$resource_module_id)
    {
        try{
            // Start a database transaction
            $deleteResourceModule = DB::transaction(function () use ($slug, $resource_module_id) {
                // Delete the resource module itself
                $deleteResourceModule = $this->resourceModuleService->deleteResourceModule($slug);
                // Delete resource module details
                $deleteResourceModuleDetailsService = $this->resourceModuleDetailsService->deleteResourceModuleDetail($resource_module_id);
                // Delete resource module skills group stack
                $deleteResourceModuleSkillsGroupStack = $this->resouceModuleSkillsGroupStackService->deleteResourceModuleSkillsGroupsStack($resource_module_id);

                $deleteResourceModuleTagsGroups=$this->resourceModuleTagsGroupsService->deleteResourceModuleTagsGroups($resource_module_id);
                // Delete resource module rating
                $deleteResourceModuleRating = $this->resourceModuleRatingService->deleteResourceModuleRating($resource_module_id);
                // Return the results of each deletion
                return [
                    "resourceModule"                 => $deleteResourceModule,
                    "resourceModuleDetails"          => $deleteResourceModuleDetailsService,
                    "resourceModuleSkillsGroupStack" => $deleteResourceModuleSkillsGroupStack,
                    "resourceModuleRating"           => $deleteResourceModuleRating,
                    "deleteResourceModuleTagsGroups" =>$deleteResourceModuleTagsGroups,
                ];
            });
            //check all tables responses
            if ($deleteResourceModule["resourceModule"] && $deleteResourceModule["resourceModuleDetails"] && $deleteResourceModule["resourceModuleSkillsGroupStack"] && $deleteResourceModule['resourceModuleRating'] && $deleteResourceModule['deleteResourceModuleTagsGroups']){
                DB::commit();
                return true;
            }
            DB::rollback();
            return false;
        }catch (\Exception $e) {
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

    public function updateResourceModule($slug, $request, $upload_cover_image){
        try {
            $createResourceModule = DB::transaction(function () use ($slug, $request, $upload_cover_image) {
                $updateResourceModule = $this->resourceModuleService->updateResourceModule($slug, $request, $upload_cover_image);
                $resourceModuleSkillsGroupStackService=$this->resouceModuleSkillsGroupStackService->updateResourceModuleSkillsGroupsStack($request,$updateResourceModule->id);
                $resourceModuleTagsGroupsService=$this->resourceModuleTagsGroupsService->updateResourceModuleTagsGroups($request,$updateResourceModule->id);

                return [
                    'updateResourceModule'           => $updateResourceModule,
                    'resourceModuleSkillsGroupsStack'=> $resourceModuleSkillsGroupStackService,
                    'resourceModuleTagsGroupsService'=> $resourceModuleTagsGroupsService,
                ];
            });
            if ($createResourceModule['updateResourceModule'] && $createResourceModule['resourceModuleSkillsGroupsStack'] && $createResourceModule['resourceModuleTagsGroupsService']) {
                DB::commit();
                return true;
            }
            DB::rollback();

            return false;
        }catch(\Exception $e) {
            return false;
        }
    }

    public function fileUpload($request,$resource_module_id,$type){
        try{
            return $this->resourceModuleDetailsService->fileUpload($request,$resource_module_id,$type);
        }catch(\Exception $e){
            return false;
        }
    }

    public function addLinks($request,$resource_module_id,$type){
        try{
            return $this->resourceModuleDetailsService->addLinks($resource_module_id,$request->title,$type,$request->path,$request->social_link_id);
        }catch(\Exception $e){
            return false;
        }
    }
}
