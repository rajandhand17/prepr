<?php

namespace App\Services\Manage;

use App\Events\ResourceModule\DeleteResourceModuleAssociatedData;
use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\ResourceModule;
use HiFolks\RandoPhp\Randomize;

class ResourceModuleService
{
    public static function getResourceModuleList($request, $organization)
    {
        try {
            $resourceModule = ResourceModule::select()->where('organization_id', '=', $organization->id);
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
                $resourceModule = $resourceModule->where('resource_modules.title', 'like', '%'.$request->search.'%');
            }

            if ($request->has('status') && !empty($request->status)) {
                switch ($request->status) {
                    case 'draft':
                        $status = config('constants.resource_module_status.draft');
                        break;
                    case 'published':
                        $status = config('constants.resource_module_status.publish');
                        break;
                    case 'archive':
                        $status = config('constants.resource_module_status.archive');
                        break;
                    default:
                        $status = null;
                }
                $resourceModule = $resourceModule->where('resource_modules.status', $status);
            }

            if ($request->has('privacy') && !empty($request->privacy)) {
                switch ($request->privacy) {
                    case 'no':
                        $privacy = config('constants.resource_module_privacy.no');
                        break;
                    case 'yes':
                        $privacy = config('constants.resource_module_privacy.yes');
                        break;
                    default:
                        $privacy = null;
                }
                $resourceModule = $resourceModule->where('resource_modules.privacy', $privacy);
            }
            if ($request->has('is_global') && !empty($request->is_global)) {
                switch ($request->is_global) {
                    case 'no':
                        $is_global = config('constants.resource_module_is_global.no');
                        break;
                    case 'yes':
                        $is_global = config('constants.resource_module_is_global.yes');
                        break;
                    default:
                        $is_global = null;
                }

                $resourceModule = $resourceModule->where('resource_modules.is_global', $is_global);
            }
            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $resourceModule = $resourceModule->orderBy('resource_modules.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $resourceModule = $resourceModule->orderBy('resource_modules.title', 'DESC');
                        break;
                    case 'creation_date':
                        $resourceModule = $resourceModule->orderBy('resource_modules.created_at', 'ASC');
                        break;
                    default:
                        $resourceModule = $resourceModule->orderBy('resource_modules.id', 'ASC');
                }
            }

            if ($request->has('skills') && !empty($request->skills) && is_array($request->skills)) {
                $resourceModule = $resourceModule->whereIn('resource_modules.id', function ($query) use ($request) {
                    $query->select('resource_module_skills_groups_stacks.resource_module_id')
                    ->from('resource_module_skills_groups_stacks')
                    ->whereIn('resource_module_skills_groups_stacks.foreign_id', $request->skills)
                        ->where('resource_module_skills_groups_stacks.type', '0')
                        ->whereNull('resource_module_skills_groups_stacks.deleted_at')
                        ->distinct();
                })->distinct('resource_modules.uuid');
            }

            if ($request->has('tags') && !empty($request->tags) && is_array($request->tags)) {
                $resourceModule = $resourceModule->whereIn('resource_modules.id', function ($query) use ($request) {
                    $query->select('resource_module_tags_groups.resource_module_id')
                    ->from('resource_module_tags_groups')
                    ->whereIn('resource_module_tags_groups.foreign_id', $request->tags)
                        ->where('resource_module_tags_groups.type', '0')
                        ->whereNull('resource_module_tags_groups.deleted_at')
                        ->distinct();
                })->distinct('resource_modules.uuid');
            }

            if ($request->has('duration_id') && $request->duration_id && is_array($request->duration_id)) {
                $resourceModule = $resourceModule->whereIn('duration_id', $request->duration_id);
            }

            if ($request->has('level_id') && $request->level_id && is_array($request->level_id)) {
                $resourceModule = $resourceModule->whereIn('level_id', $request->level_id);
            }

            return $resourceModule;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function getResourceModuleBasedOnSlug($slug)
    {
        try {
            return ResourceModule::select()->where('slug', $slug)->first();
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function deleteResourceModule($resource_module_id)
    {
        try {
            $resourceModule = ResourceModule::find($resource_module_id)->delete();
            if ($resourceModule) {
                $associatedResourceModule = event(new DeleteResourceModuleAssociatedData($resource_module_id));

                return true;
            }

            return false;
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

    public function createResourceModule($request, $upload_cover_image)
    {
        try {
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            $status = config('constants.resource_module_status.draft');
            switch($request->status) {
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
            switch ($request->is_global) {
                case 'no':
                    $is_global = config('constants.resource_module_is_global.no');
                    break;
                case 'yes':
                    $is_global = config('constants.resource_module_is_global.yes');
                    break;
                default:
                    $is_global = null;
            }
            switch ($request->privacy) {
                case 'no':
                    $privacy = config('constants.resource_module_privacy.no');
                    break;
                case 'yes':
                    $privacy = config('constants.resource_module_privacy.yes');
                    break;
                default:
                    $privacy = null;
            }
            $model = new ResourceModule();
            $slug = UtilityHelper::generateSlug($request->title, $model);
            $resourceModule = new ResourceModule();
            $resourceModule->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $resourceModule->language = $request->language;
            $resourceModule->user_id = auth()->user()->id;
            $resourceModule->organization_id = $organization->id;
            $resourceModule->duration_id = $request->duration_id;
            $resourceModule->level_id = $request->level_id;
            $resourceModule->title = $request->title;
            $resourceModule->slug = $slug;
            $resourceModule->description = $request->description;
            $resourceModule->media = $upload_cover_image;
            $resourceModule->privacy = $privacy;
            $resourceModule->status = $status;
            $resourceModule->is_global = $is_global;
            $resourceModule->save();

            return $resourceModule;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function uploadResourceModuleCoverImage($cover_image)
    {
        try {
            $upload_resource_module_cover_image = FileUploadHelper::uploadImageToS3($cover_image, 'resource_module');
            if ($upload_resource_module_cover_image == false) {
                return false;
            }

            return $upload_resource_module_cover_image;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateResourceModule($slug, $request, $cover_image)
    {
        try {
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            $status = config('constants.resource_module_status.draft');
            switch($request->status) {
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
            switch ($request->is_global) {
                case 'no':
                    $is_global = config('constants.resource_module_is_global.no');
                    break;
                case 'yes':
                    $is_global = config('constants.resource_module_is_global.yes');
                    break;
                default:
                    $is_global = null;
            }
            switch ($request->privacy) {
                case 'no':
                    $privacy = config('constants.resource_module_privacy.no');
                    break;
                case 'yes':
                    $privacy = config('constants.resource_module_privacy.yes');
                    break;
                default:
                    $privacy = null;
            }
            $resourceModule = ResourceModule::where('slug', $slug)->first();
            $resourceModule->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $resourceModule->language = $request->language;
            $resourceModule->duration_id = ($request->has('duration_id')) ? $request->duration_id : $resourceModule->duration_id;
            $resourceModule->level_id = ($request->has('level_id')) ? $request->level_id : $resourceModule->level_id;
            $resourceModule->title = $request->title;
            $resourceModule->description = $request->description;
            $resourceModule->media = $cover_image;
            $resourceModule->privacy = $privacy;
            $resourceModule->status = $status;
            $resourceModule->is_global = $is_global;
            $resourceModule->save();

            return $resourceModule;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getResourceModuleBasedOnUUIDArray($resourceModuleUUIDArray)
    {
        try {
            $resourceModuleIds = ResourceModule::whereIn('uuid', $resourceModuleUUIDArray)->pluck('id')->all();
            if ($resourceModuleIds != null) {
                return $resourceModuleIds;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getResourceModuleBasedOnId($id)
    {
        try {
            return ResourceModule::select('id', 'uuid', 'title', 'media', 'slug', 'description')->where('id', $id)->first();
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getResourceModuleGetBasedId($id)
    {
        try {
            $resourceModuleIds = ResourceModule::whereIn('id', $id)->pluck('id')->all();
            if ($resourceModuleIds != null) {
                return $resourceModuleIds;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getListName($request, $organization)
    {
        try {
            $resourceModule = ResourceModule::select('uuid', 'title', 'media')->where('organization_id', '=', $organization->id);
            $resourceModule = self::filterResourceModuleList($request, $resourceModule);

            return $resourceModule->paginate(config('site-settings.pagination_per_page'));
        } catch(\Exception $e) {
            return false;
        }
    }
}
