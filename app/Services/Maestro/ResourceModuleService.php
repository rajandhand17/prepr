<?php

namespace App\Services\Maestro;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\ResourceModule;
use Exception;
use HiFolks\RandoPhp\Randomize;

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

    public static function createAndUpdateResourceModule($request, $action, $id)
    {
        try {
            $resourceCoverImage = self::resourceModuleCoverImageUpload($request);
            if ($action == 'create') {
                $model = new ResourceModule();
                $resourceModule = new ResourceModule();
                $resourceModule->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
                $resourceModule->slug = UtilityHelper::generateSlug($request->title, $model);
                $resourceModule->language = $request->language;
            } elseif ($action == 'update') {
                $resourceModule = ResourceModule::find($id);
                $resourceCoverImage = $resourceModule->media;
            }

            $resourceModule->user_id = $request->user_id;
            $resourceModule->title = $request->title;
            $resourceModule->description = $request->description;
            $resourceModule->organization_id = $request->organization_id;
            $resourceModule->privacy = $request->privacy;
            $resourceModule->status = $request->status;
            $resourceModule->media = $resourceCoverImage ? $resourceCoverImage : null;

            if ($resourceModule->save()) {
                return $resourceModule;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function resourceModuleCoverImageUpload($request)
    {
        try {
            $coverImage = null;
            if ($request->file('cover_image')) {
                $coverImage = FileUploadHelper::uploadImageToS3($request->file('cover_image'), 'resource_module');
            }

            return $coverImage;
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

    public static function getResourceModulesByIds($moduleIds)
    {
        try {
            return ResourceModule::whereIn('id', $moduleIds)->pluck('title', 'id');
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getResourceModulesById($request)
    {
        try {
            $resourceOrg = ResourceModule::where(['organization_id'=> (int) $request->org_id, 'language' => $request->language])->pluck('id')->toArray();
            $resourceGlobal = ResourceModule::where(['is_global' => '1'])->pluck('id')->toArray();
            $resourceList = array_merge($resourceOrg, $resourceGlobal);
            $resourceJson = ResourceModule::whereIn('id', $resourceList)->orderBy('id', 'DESC');

            if ($request->search) {
                $resourceJson->where('title', 'LIKE', '%'.$request->search.'%');
            }
            $resourceJsons = $resourceJson->pluck('title', 'id');
            $total_count = $resourceJsons->count();
            $resourceResults = $jsonTags = [];
            $count = 0;
            foreach ($resourceJsons as $key => $tag) {
                $resourceResults[$count]['id'] = $key;
                $resourceResults[$count]['text'] = $tag;
                $count++;
            }
            $jsonTags['result'] = $resourceResults;
            $jsonTags['more'] = true;
            $jsonTags['total_count'] = $total_count;

            return response()->json($jsonTags);
        } catch (Exception $e) {
            return false;
        }
    }
}
