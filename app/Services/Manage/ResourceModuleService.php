<?php

namespace App\Services\Manage;

use App\Events\ResourceModule\DeleteResourceModuleAssociatedData;
use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\ResourceModule;
use App\Models\ResourceModuleSkillsGroupsStack;
use App\Services\ModuleCompletionStatusService;
use App\Services\SkillService;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Support\Facades\Log;

class ResourceModuleService
{
    public function getResourceModuleCountBasedOnOrganization($organizationId)
    {
        try {
            $resourceModule_count = ResourceModule::where(['organization_id' => $organizationId, 'is_auto_created' => '0'])->count();

            return $resourceModule_count;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceModuleList($request, $organization)
    {
        try {
            $resourceModule = ResourceModule::select()->where('organization_id', '=', $organization->id);
            $resourceModule = self::filterResourceModuleList($request, $resourceModule);

            return $resourceModule->paginate(config('site-settings.pagination_per_page'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function filterResourceModuleList($request, $resourceModule)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $resourceModule = $resourceModule->whereSearchFilter($request->search ?? '');
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
                    case 'public':
                        $privacy = config('constants.resource_module_privacy.no');
                        break;
                    case 'private':
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

            if ($request->has('duration_id') && $request->duration_id && is_array($request->duration_id)) {
                $resourceModule = $resourceModule->whereIn('duration_id', $request->duration_id);
            }

            if ($request->has('level_id') && $request->level_id && is_array($request->level_id)) {
                $resourceModule = $resourceModule->whereIn('level_id', $request->level_id);
            }
            if ($request->has('rating') && !empty($request->rating)) {
                $resourceModuleRating = ResourceModuleRatingService::getResourceModuleBasedOnRating($request->rating);
                $resourceModule = $resourceModule->whereIn('id', $resourceModuleRating->pluck('resource_module_id'));
            }
            if ($request->has('type') && !empty($request->type)) {
                $resourceModuleType = ResourceModuleTypeModesService::getResourceModuleBasedOnType($request->type);
                $resourceModule = $resourceModule->whereIn('id', $resourceModuleType->pluck('resource_module_id'));
            }
            if ($request->has('progress') && !empty($request->progress)) {
                $resourceModulesProgress = [];
                $moduleType = config('constants.module_completion_statuses_types.resource_module');
                switch ($request->progress) {
                    case 'not-started':
                        $resourceModulesProgress = ModuleCompletionStatusService::getResourceProgress($moduleType, config('constants.status_module_completion.not_started'));
                        break;
                    case 'in-progress':
                        $resourceModulesProgress = ModuleCompletionStatusService::getResourceProgress($moduleType, config('constants.status_module_completion.in_progress'));
                        break;
                    case 'complete':
                        $resourceModulesProgress = ModuleCompletionStatusService::getResourceProgress($moduleType, config('constants.status_module_completion.completed'));
                        break;
                }

                $resourceModule = $resourceModule->whereIn('id', $resourceModulesProgress->pluck('module_id'));
            }

            return $resourceModule;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceModuleBasedOnSlug($slug)
    {
        try {
            return ResourceModule::select()->where('slug', $slug)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceModuleBasedOnTitle($title)
    {
        try {
            $userId = auth()->id();
            $resourceGroup = ResourceModule::where(['title'=>$title, 'user_id'=>auth()->user()->id])->first();

            return $resourceGroup;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

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
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkName($title)
    {
        try {
            return ResourceModule::where('title', $title)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createResourceModule($request, $upload_cover_image, $organizationId, $is_go1 = false)
    {
        try {
            $status = config('constants.resource_module_status.draft');
            switch ($request->status) {
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

            $is_global = config('constants.resource_module_is_global.no');
            switch ($request->is_global) {
                case 'no':
                    $is_global = config('constants.resource_module_is_global.no');
                    break;
                case 'yes':
                    $is_global = config('constants.resource_module_is_global.yes');
                    break;
                default:
                    $is_global = config('constants.resource_module_is_global.no');
            }
            $privacy = null;
            switch ($request->privacy) {
                case 'public':
                    $privacy = config('constants.resource_module_privacy.no');
                    break;
                case 'private':
                    $privacy = config('constants.resource_module_privacy.yes');
                    break;
                default:
                    $privacy = null;
            }
            function getIsAiCreatedValue($isAiCreatedInput)
            {
                if ($isAiCreatedInput === 'yes' || $isAiCreatedInput === true) {
                    return config('constants.challenge_ai_created.yes');
                } elseif ($isAiCreatedInput === 'no' || $isAiCreatedInput === false) {
                    return config('constants.challenge_ai_created.no');
                } else {
                    return config('constants.challenge_ai_created.no');
                }
            }

            $is_ai_created = config('constants.challenge_ai_created.no');

            if ($request->is_ai_created !== null) {
                $is_ai_created = getIsAiCreatedValue($request->is_ai_created);
            } elseif (isset($request->go1_course['is_ai_created'])) {
                $is_ai_created = getIsAiCreatedValue($request->go1_course['is_ai_created']);
            }

            $go1Course = $is_go1 ? $request->go1_course : null;
            $title = $is_go1 ? data_get($go1Course, 'title') : $request->title;
            $description = $is_go1 ? data_get($go1Course, 'description') : $request->description;

            if ($is_go1) {
                $privacy = config('constants.resource_module_privacy.yes');
                $status = config('constants.resource_module_status.draft');
                $is_global = config('constants.resource_module_is_global.no');
                $duration_id = $request->go1_course['duration_id'] ?? null;
            } else {
                $duration_id = $request->duration_id;
            }
            $media_type = config('constants.resource_media_type.image');
            switch ($request->media_type) {
                case 'image':
                    $media_type = config('constants.resource_media_type.image');
                    break;
                case 'embedded':
                    $media_type = config('constants.resource_media_type.embedded');
                    break;
            }
            $model = new ResourceModule();
            $slug = UtilityHelper::generateSlug($title, $model);
            $resourceModule = new ResourceModule();
            $resourceModule->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $resourceModule->language = $request->language;
            $resourceModule->user_id = auth()->user()->id;
            $resourceModule->organization_id = $organizationId;
            $resourceModule->duration_id = $duration_id;
            $resourceModule->level_id = $request->level_id;
            $resourceModule->title = $title;
            $resourceModule->slug = $slug;
            $resourceModule->description = $description;
            $resourceModule->media = $upload_cover_image;
            $resourceModule->media_type = $media_type;
            $resourceModule->privacy = $privacy;
            $resourceModule->status = $status;
            $resourceModule->is_global = $is_global;
            $resourceModule->is_ai_created = $is_ai_created;
            $resourceModule->go1_course_id = $is_go1 ? $go1Course['id'] : null;
            $resourceModule->go1_metadata = $is_go1 ? $go1Course : null;
            $resourceModule->is_accessible = '1';
            $resourceModule->save();

            return $resourceModule;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in createResourceModule in ResourceModuleService.php: '.$e->getMessage());

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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateResourceModule($slug, $request, $cover_image, $organizationId)
    {
        try {
            $status = config('constants.resource_module_status.draft');
            switch ($request->status) {
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
                case 'public':
                    $privacy = config('constants.resource_module_privacy.no');
                    break;
                case 'private':
                    $privacy = config('constants.resource_module_privacy.yes');
                    break;
                default:
                    $privacy = null;
            }
            $media_type = config('constants.resource_media_type.image');
            switch ($request->media_type) {
                case 'image':
                    $media_type = config('constants.resource_media_type.image');
                    break;
                case 'embedded':
                    $media_type = config('constants.resource_media_type.embedded');
                    break;
                default:
                    $media_type = null;
            }
            $resourceModule = ResourceModule::where('slug', $slug)->first();
            $resourceModule->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $resourceModule->language = $request->language;
            $resourceModule->duration_id = ($request->has('duration_id')) ? $request->duration_id : $resourceModule->duration_id;
            $resourceModule->level_id = ($request->has('level_id')) ? $request->level_id : $resourceModule->level_id;
            $resourceModule->title = $request->title;
            $resourceModule->description = $request->description;
            $resourceModule->organization_id = $organizationId;
            $resourceModule->media =  ($cover_image != null) ? $cover_image : $resourceModule->cover_image;
            $resourceModule->media_type = ($request->has('media_type')) ? $media_type : $resourceModule->media_type;
            $resourceModule->privacy = $privacy;
            $resourceModule->status = $status;
            $resourceModule->is_global = $is_global;
            $resourceModule->is_accessible = '1';
            $resourceModule->save();

            return $resourceModule;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceModuleBasedOnId($id)
    {
        try {
            return ResourceModule::select('id', 'uuid', 'title', 'media', 'slug', 'description')->where(['id' => $id, 'is_accessible' => '1'])->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceModulesBasedOnId($id)
    {
        try {
            return ResourceModule::where(['id' => $id, 'is_accessible' => '1'])->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getListName($request, $organization)
    {
        try {
            $resourceModule = ResourceModule::select('uuid', 'title', 'media')->where(['organization_id' => $organization->id, 'is_accessible' => '1']);
            $resourceModule = self::filterResourceModuleList($request, $resourceModule);

            return $resourceModule->paginate(config('site-settings.pagination_per_page'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceModuleBasedOnGO1Id($go1CourseId)
    {
        try {
            return ResourceModule::where('go1_course_id', $go1CourseId)->first();
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function getResourceModuleBasedOnUUID($uUID)
    {
        try {
            return ResourceModule::select('id', 'uuid', 'title', 'media', 'slug', 'description')->where('UUID', $uUID)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function storeGO1Skills($resourceModuleId, $skills = [])
    {
        try {
            $skillsIds = SkillService::createSkillFromGO1($skills);

            if (count($skills) > 0) {
                foreach ($skillsIds as $id) {
                    $ResourceModuleGroupsStack = new ResourceModuleSkillsGroupsStack();
                    $ResourceModuleGroupsStack->resource_module_id = $resourceModuleId;
                    $ResourceModuleGroupsStack->foreign_id = $id;
                    $ResourceModuleGroupsStack->type = '0';
                    $ResourceModuleGroupsStack->save();
                }
            }

            return true;
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function deleteOrganizationResourceModule($organizationId)
    {
        try {
            $fetchOrganizationResourceModules = ResourceModule::where('organization_id', $organizationId)->pluck('id');
            if (!empty($fetchOrganizationResourceModules)) {
                foreach ($fetchOrganizationResourceModules as $organizationResourceModule) {
                    $deleteOrganizationResourceModule = self::deleteResourceModule($organizationResourceModule);
                    if (!$deleteOrganizationResourceModule) {
                        return false;
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourcesWithRelations($id)
    {
        try {
            return ResourceModule::with('skills_group_stack', 'resource_module_type_modes')->find($id);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function cloneResourceModule($getResourceModule)
    {
        try {
            $resourceModule = new ResourceModule();
            $uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $slug = UtilityHelper::generateSlug($getResourceModule->title.$uuid, $resourceModule);
            $mediaType = ($getResourceModule->media_type == '' || $getResourceModule->media_type == 'image')
                ? config('constants.resource_media_type.image')
                : config('constants.resource_media_type.embedded');
            $resourceModule = $getResourceModule->replicate();
            $resourceModule->uuid = $uuid;
            $resourceModule->slug = $slug;
            $resourceModule->media_type = $mediaType;
            $resourceModule->user_id = auth()->user()->id;
            $resourceModule->save();

            return $resourceModule;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchResourceModuleReportBasedOnOrganization($organizationId)
    {
        try {
            $fetchResourceModule = ResourceModule::where(['organization_id' => $organizationId, 'status' => '1', 'is_accessible' => '1'])->get();

            return $fetchResourceModule;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
