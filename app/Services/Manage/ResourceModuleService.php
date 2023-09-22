<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\ResourceModule;
use HiFolks\RandoPhp\Randomize;

class ResourceModuleService
{
    public static function getResourceModuleList($request)
    {
        try {
            $resourceModule = ResourceModule::select();
            $resourceModule = self::filterResourceModuleList($request, $resourceModule);
            return $resourceModule->paginate(config('site-settings.pagination_per_page'));
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function filterResourceModuleList($request, $resourceModule)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $resourceModule = $resourceModule->where('resource_module.title', 'like', '%'.$request->search.'%');
            }
            return $resourceModule;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getResourceModuleBasedOnSlug($slug){
        try {
            return ResourceModule::select()->where('slug',$slug)->first();
        }catch(\Exception $e){
            return false;
        }
    }

    public function checkSlug($slug)
    {
        try {
            return ResourceModule::where('slug', $slug)->first();
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function delete($slug)
    {
        try {
            return ResourceModule::where('slug', $slug)->delete();
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function checkName($title)
    {
        try {
            return ResourceModule::where('title', $title)->first();
        } catch(\Exception $e) {
            return false;
        }
    }

    public function createResourceModule($request,$media){
        try{
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            $status = config('constants.resource_module_status.draft');
            switch($request->status){
                case 'publish':
                    $status = config('constants.resource_module_status.publish');
                    break;
                case 'archive':
                    $status = config('constants.resource_module_status.archive');
                    break;
                default:
                    $status = config('constants.resource_module_status.draft');
                    break;
            }
            $model=new ResourceModule();
            $slug = UtilityHelper::generateSlug($request->title, $model);
            $resourceModule=new ResourceModule();
            $resourceModule->uuid=Randomize::chars(10)->alphanumeric()->unique()->generate();
            $resourceModule->language = $request->language;
            $resourceModule->user_id = auth()->user()->id;
            $resourceModule->organization_id = $organization->id;
            $resourceModule->title = $request->title;
            $resourceModule->slug = $slug;
            $resourceModule->description = $request->description;
            $resourceModule->media_type=$request->media_type;
            $resourceModule->media=$media;
            $resourceModule->privacy=($request->privacy=='yes')?'1':'0';
            $resourceModule->status=$status;
            $resourceModule->is_global=($request->is_global=='yes')?'1':'0';
            $resourceModule->save();
            return $resourceModule;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function uploadResourceModuleMedia($image){
        try {
            $upload_resource_module_cover_image = FileUploadHelper::uploadImageToS3($image, 'resource_module');
            if ($upload_resource_module_cover_image == false){
                return false;
            }
            return $upload_resource_module_cover_image;
        } catch (\Exception $e) {
            return false;
        }
    }
}
