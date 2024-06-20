<?php

namespace App\Services\Maestro\Resource;

use App\Models\Organization;
use App\Models\ResourceModule;
use App\Models\User;
use App\Models\Language;
use App\Helpers\UtilityHelper;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Support\Facades\Storage;
use Exception;

class ResourceModuleService
{
    public static function getResourceModuleList()
    {
        try {
            return ResourceModule::latest();
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getResourceModuleUser()
    {
        try {
            $users = User::pluck('username', 'id')->prepend('Please Select', '');
            return $users;
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getResourceModuleOrganization()
    {
        try {
            $organization = Organization::pluck('title', 'id')->prepend('Please Select organization', '');
            return $organization;
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getLanguage()
    {
        try {
            $language = Language::where(['status' => 1])->pluck('name', 'iso');
            if ($language != null) {
                return $language;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public static function createResourceModule($request)
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

            $model = new ResourceModule();
            $slug = UtilityHelper::generateSlug($request->title, $model);
            $resourceModule = new ResourceModule();
            $resourceModule->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $resourceModule->language    = $request->language;
            $resourceModule->user_id     = $request->user_id;
            $resourceModule->title       = $request->title;
            $resourceModule->slug        = $slug;
            $resourceModule->description = $request->description;
            $resourceModule->organization_id = $request->organization_id;
            $resourceModule->privacy    = $request->privacy;
            $resourceModule->status     = $request->status;
            $resourceModule->media      = $webp_path_cover;
            
            if ($resourceModule->save()) {
                return true;
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
    public static function getResourceModuleStatus()
    {
        try {
            return ['0' => 'Draft', '1' => 'Published','2' => 'Archive'];    
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getResourceModulePrivacy()
    {
        try {
            return ['0' => 'Not available globally', '1' => 'Available globally'];    
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
    public static function updateResourceModuleById($id, $request)
    {
        try {
            $resourceModule = ResourceModule::find($id);
            if (!empty($resourceModule)) {
                    if ($request->file('cover_image')) {
                        $pathsArray = config('s3-upload-path');
                        $file = $request->file('cover_image');
                        $image_contents_cover = fopen($file->getRealPath(), 'rb');
                        $webp_path_cover = $pathsArray['resource_module'].time().'.webp';
                        Storage::disk('s3')->put($webp_path_cover, $image_contents_cover);
                    } else {
                        $webp_path_cover    = $resourceModule->media;
                    }
                    $resourceModule->title       = $request->title;
                    $resourceModule->user_id     = $request->user_id;
                    $resourceModule->description = $request->description;
                    $resourceModule->organization_id = $request->organization_id;
                    $resourceModule->privacy     = $request->privacy;
                    $resourceModule->status      = $request->status;
                    $resourceModule->media       = $webp_path_cover;
                if ($resourceModule->save()) {
                    return true;
                }
                return false;
            }
            return false;
        } catch (Exception $e) {
            dd($e->getMessage());
            return false;
        }
    }
}
