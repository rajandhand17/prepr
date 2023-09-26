<?php

namespace App\Repositories\Api\Manage\ResourceModule;

use App\Services\Manage\ResourceModuleDetailService;
use App\Services\Manage\ResourceModuleRatingService;
use App\Services\Manage\ResourceModuleService;
use App\Services\Manage\ResourceModuleSkillsGroupsStackService;
use DB;

class ResourceModuleRepository implements ResourceModuleInterface
{
    protected $resourceModuleService;

    protected $resourceModuleDetailsService;

    protected $resouceModuleSkillsGroupStackService;

    protected $resourceModuleRatingService;

    public function __construct(ResourceModuleService $resourceModuleService,ResourceModuleDetailService $resourceModuleDetailsService,ResourceModuleSkillsGroupsStackService $resouceModuleSkillsGroupStackService,ResourceModuleRatingService $resourceModuleRatingService)
    {
        $this->resourceModuleService = $resourceModuleService;
        $this->resourceModuleDetailsService = $resourceModuleDetailsService;
        $this->resouceModuleSkillsGroupStackService=$resouceModuleSkillsGroupStackService;
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
            return  $this->resourceModuleService->createResourceModule($request,$upload_cover_image);
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
                $deleteResourceModule = $this->resourceModuleService->delete($slug);
                // Delete resource module details
                $deleteResourceModuleDetailsService = $this->resourceModuleDetailsService->delete($resource_module_id);
                // Delete resource module skills group stack
                $deleteResourceModuleSkillsGroupStack = $this->resouceModuleSkillsGroupStackService->delete($resource_module_id);
                // Delete resource module rating
                $deleteResourceModuleRating = $this->resourceModuleRatingService->delete($resource_module_id);
                // Return the results of each deletion
                return [
                    "resourceModule" => $deleteResourceModule,
                    "resourceModuleDetails" => $deleteResourceModuleDetailsService,
                    "resourceModuleSkillsGroupStack" => $deleteResourceModuleSkillsGroupStack,
                    "resourceModuleRating" => $deleteResourceModuleRating,
                ];
            });
            //check all tables responses
            if ($deleteResourceModule["resourceModule"] && $deleteResourceModule["resourceModuleDetails"] && $deleteResourceModule["resourceModuleSkillsGroupStack"] && $deleteResourceModule['resourceModuleRating']){
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
            return $this->resourceModuleService->updateResourceModule($slug, $request, $upload_cover_image);
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
