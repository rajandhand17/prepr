<?php

namespace App\Services\Maestro\Resource;

use App\Helpers\UtilityHelper;
use App\Models\ResourceModule;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Support\Facades\Storage;
use App\Services\Maestro\LanguageService;

class ResourceModuleService
{
    public static function getResourceModuleList()
    {
        try {
            return ResourceModule::where('language', LanguageService::getCurrentLanguage())->latest();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createAndUpdateResourceModule($request,$action,$id)
    {
        try {
            if ($request->file('cover_image')) {
                $pathsArray = config('s3-upload-path');
                $file = $request->file('cover_image');
                $image_contents_cover = fopen($file->getRealPath(), 'rb');
                $webp_path_cover = $pathsArray['resource_module'].time().'.webp';
                Storage::disk('s3')->put($webp_path_cover, $image_contents_cover);
            } else {
                $webp_path_cover = null;
            }

            if($action == 'create') {
                $model = new ResourceModule();
                $resourceModule         = new ResourceModule();
                $resourceModule->uuid   = Randomize::chars(10)->alphanumeric()->unique()->generate();
                $resourceModule->slug   = UtilityHelper::generateSlug($request->title, $model);
                $resourceModule->language = $request->language;
            } else if($action == 'update') {
                $resourceModule = ResourceModule::find($id);
                $webp_path_cover = $resourceModule->media;
            }

            $resourceModule->user_id    = $request->user_id;
            $resourceModule->title      = $request->title;
            $resourceModule->description= $request->description;
            $resourceModule->organization_id = $request->organization_id;
            $resourceModule->privacy    = $request->privacy;
            $resourceModule->status     = $request->status;
            $resourceModule->media      = $webp_path_cover;

            if ($resourceModule->save()) {
                return $resourceModule;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteResourceModule($id)
    {
        try {
            $socialLink = ResourceModule::find($id);
            if (!empty($socialLink)) {
                return $socialLink->delete();
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getResourceModuleById($id)
    {
        try {
            $socialLink = ResourceModule::findOrFail($id);
            if ($socialLink != null) {
                return $socialLink;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
