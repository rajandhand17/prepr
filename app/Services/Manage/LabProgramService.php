<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\LabProgram;
use App\Services\Public\LabProgramSocialActivitiesService;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Database\Eloquent\Collection;

class LabProgramService
{
    public function getLabProgramCountBasedOnOrganization($organizationId)
    {
        try {
            $labProgram_count = LabProgram::where(['organization_id' => $organizationId, 'is_auto_created' => '0'])->count();

            return $labProgram_count;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getLabProgramList($request, $organization)
    {
        $getLabProgramList = LabProgram::select()->where('organization_id', '=', $organization->id);
        $getLabProgramList = self::filterLabProgramList($getLabProgramList, $request);

        return $getLabProgramList->paginate(config('site-settings.pagination_per_page'));
    }

    public function filterLabProgramList($labProgramList, $request)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                //$labProgramList = $labProgramList->where('lab_programs.title', 'like', '%'.$request->search.'%');
                $labProgramList = $labProgramList->whereSearchFilter($request->search ?? '');
            }
            if ($request->has('category') && !empty($request->category) && is_array($request->category)) {
                $labProgramList = $labProgramList->whereIn('lab_programs.category_id', $request->category);
            }
            if ($request->filled('social_type') && in_array($request->social_type, ['liked', 'favourites'])) {
                $activityType = ($request->social_type == 'liked') ? 'like' : 'favourite';
                $labIds = LabProgramSocialActivitiesService::getLabProgramsBasedOnActivity($activityType)->pluck('lab_program_id');
                $labProgramList->whereIn('lab_programs.id', $labIds);
            }
            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $labProgramList->orderBy('lab_programs.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $labProgramList->orderBy('lab_programs.title', 'DESC');
                        break;
                    case 'creation_date':
                        $labProgramList->orderBy('lab_programs.created_at', 'ASC');
                        break;
                    default:
                        $labProgramList->orderBy('lab_programs.id', 'ASC');
                }
            }

            if ($request->has('privacy') && !empty($request->privacy)) {
                switch ($request->privacy) {
                    case 'public':
                        $labProgramList = $labProgramList->where('lab_programs.privacy', '0');
                        break;
                    case 'private':
                        $labProgramList = $labProgramList->where('lab_programs.privacy', '1');
                        break;
                    default:
                        $labProgramList = $labProgramList;
                }
            }
            if ($request->has('skills') && !empty($request->skills) && is_array($request->skills)) {
                $labProgramList = $labProgramList->whereIn('lab_programs.id', function ($query) use ($request) {
                    $query->select('lab_programs_skills_groups_stack.lab_program_id')
                        ->from('lab_programs_skills_groups_stack')
                        ->whereIn('lab_programs_skills_groups_stack.foreign_id', $request->skills)
                        ->where('lab_programs_skills_groups_stack.type', '0')
                        ->whereNull('lab_programs_skills_groups_stack.deleted_at')
                        ->distinct();
                })->distinct('lab_programs.uuid');
            }
            if ($request->has('tags') && !empty($request->tags) && is_array($request->tags)) {
                $labProgramList = $labProgramList->whereIn('lab_programs.id', function ($query) use ($request) {
                    $query->select('lab_programs_tags_groups.lab_program_id')
                        ->from('lab_programs_tags_groups')
                        ->whereIn('lab_programs_tags_groups.foreign_id', $request->tags)
                        ->where('lab_programs_tags_groups.type', '0')
                        ->whereNull('lab_programs_tags_groups.deleted_at')
                        ->distinct();
                })->distinct('lab_programs.uuid');
            }
            if ($request->has('duration_id') && $request->duration_id && is_array($request->duration_id)) {
                $labProgramList = $labProgramList->whereIn('duration_id', $request->duration_id);
            }
            if ($request->has('level_id') && $request->level_id && is_array($request->level_id)) {
                $labProgramList = $labProgramList->whereIn('level_id', $request->level_id);
            }

            if ($request->has('type') && !empty($request->type)) {
                $typeFilterIds = LabProgramTypeModesService::getLabProgramType($request->type);
                $labProgramList = $labProgramList->whereIn('lab_programs.id', $typeFilterIds);
            }
            if ($request->has('source') && !empty($request->source)) {
                $sourceLabIds = self::getLabProgramBaseOnSource($request->source);
                $labProgramList = $labProgramList->whereIn('lab_programs.id', $sourceLabIds);
            }

            return $labProgramList;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabProgramBaseOnSource($source)
    {
        try {
            $createdByYouLabIds = collect([]);$onboardingLabIds = collect([]); $clonedByYouLabIds = collect([]); $createdByOrgLabIds = collect([]);
            if(in_array('created_by_you',$source)){
                $createdByYouLabIds = LabProgram::where(['user_id' => auth('api')->user()->id])->pluck('id');
            }
            if(in_array('onboarding_challenge',$source)){
                $onboardingLabIds = LabProgram::where(['is_auto_created' => '1'])->pluck('id');
            }
            if(in_array('created_by_organizations',$source)){
                $userData = auth()->user();
                $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
                $createdByOrgLabIds = LabProgram::where(['organization_id' => $organization->id])->pluck('id');
            }
            $labsCollection = new Collection;
            $labsCollection = $labsCollection->concat($createdByYouLabIds);
            $labsCollection = $labsCollection->concat($onboardingLabIds);
            $labsCollection = $labsCollection->concat($clonedByYouLabIds);
            $labsCollection = $labsCollection->concat($createdByOrgLabIds);
            return $labsCollection;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
    
    public static function getSourceByLabProgramId($labProgramId)
    {
        try {
            if(LabProgram::where(['id' => $labProgramId,'user_id' => auth('api')->user()->id])->exists()) {
                $source = 'created_by_you';
            } else if (LabProgram::where(['id' => $labProgramId,'is_auto_created' => '1'])->exists()) {
                $source = 'onboarding_challenge';
            } else {
                $source = 'created_by_organizations';
            }
            return $source;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
    public static function getLabProgramBasedOnSlug($slug)
    {
        try {
            return LabProgram::where('slug', $slug)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createLabProgram($request, $upload_media, $organizationId)
    {
        try {
            $privacy = config('constants.lab_privacy.no');
            switch($request->privacy) {
                case 'yes':
                    $privacy = config('constants.lab_privacy.yes');
                    break;
                case 'no':
                    $privacy = config('constants.lab_privacy.no');
                    break;
                default:
                    $privacy = config('constants.lab_privacy.yes');
                    break;
            }

            $status = config('constants.lab_status.draft');
            switch($request->status) {
                case 'draft':
                    $status = config('constants.lab_status.draft');
                    break;
                case 'publish':
                    $status = config('constants.lab_status.publish');
                    break;
                case 'archive':
                    $status = config('constants.lab_status.archive');
                    break;
                default:
                    $status = config('constants.lab_status.draft');
                    break;
            }
            $model = new LabProgram();
            $slug = UtilityHelper::generateSlug($request->title, $model);
            $labProgram = new LabProgram();
            $labProgram->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $labProgram->language = $request->language;
            $labProgram->title = $request->title;
            $labProgram->slug = $slug;
            $labProgram->description = $request->description;
            $labProgram->organization_id = $organizationId;
            $labProgram->category_id = $request->category_id;
            $labProgram->duration_id = $request->duration_id;
            $labProgram->level_id = $request->level_id;
            $labProgram->user_id = auth()->user()->id;
            $labProgram->media_type = 'image';
            $labProgram->media = $upload_media;
            $labProgram->privacy = $privacy;
            $labProgram->status = $status;
            $labProgram->is_auto_created = '0';
            $labProgram->is_sequential = ($request->is_sequential == 'yes') ? '1' : '0';
            $labProgram->is_achievement_enabled = ($request->is_achievement_enabled == 'yes') ? '1' : '0';
            $labProgram->save();

            return $labProgram;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function uploadLabProgramMedia($image)
    {
        try {
            $upload_lab_cover_image = FileUploadHelper::uploadImageToS3($image, 'lab_program');
            if ($upload_lab_cover_image == false) {
                return false;
            }

            return $upload_lab_cover_image;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkSlug($slug)
    {
        try {
            return LabProgram::where('slug', $slug)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function delete($slug)
    {
        try {
            return LabProgram::where('slug', $slug)->delete();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkNameExistsOrNot($title)
    {
        try {
            $checkLabProgramName = LabProgram::where('title', $title)->first();
            if ($checkLabProgramName) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateLabProgram($slug, $request, $upload_media, $organizationId)
    {
        try {
            $labProgram = LabProgram::where('slug', $slug)->first();
            $privacy = config('constants.lab_privacy.no');
            switch($request->privacy) {
                case 'yes':
                    $privacy = config('constants.lab_privacy.yes');
                    break;
                case 'no':
                    $privacy = config('constants.lab_privacy.no');
                    break;
                default:
                    $privacy = config('constants.lab_privacy.yes');
                    break;
            }

            $status = config('constants.lab_status.draft');
            switch($request->status) {
                case 'draft':
                    $status = config('constants.lab_status.draft');
                    break;
                case 'publish':
                    $status = config('constants.lab_status.publish');
                    break;
                case 'archive':
                    $status = config('constants.lab_status.archive');
                    break;
                default:
                    $status = config('constants.lab_status.draft');
                    break;
            }
            $labProgram->language = ($request->has('language')) ? $request->language : $labProgram->language;
            $labProgram->title = ($request->has('title')) ? $request->title : $labProgram->title;
            $labProgram->description = ($request->has('description')) ? $request->description : $labProgram->description;
            $labProgram->organization_id = $organizationId;
            $labProgram->category_id = ($request->has('category_id')) ? $request->category_id : $labProgram->category_id;
            $labProgram->duration_id = ($request->has('duration_id')) ? $request->duration_id : $labProgram->duration_id;
            $labProgram->level_id = ($request->has('level_id')) ? $request->level_id : $labProgram->level_id;
            $labProgram->media = ($upload_media) ? $upload_media : $labProgram->media;
            $labProgram->privacy = $privacy;
            $labProgram->status = $status;
            $labProgram->is_auto_created = '0';
            $labProgram->is_sequential = $request->has('is_sequential') ? ($request->is_sequential == 'yes' ? '1' : '0') : $labProgram->is_sequential;
            $labProgram->is_achievement_enabled = $request->has('is_achievement_enabled') ? ($request->is_achievement_enabled == 'yes' ? '1' : '0') : $labProgram->is_achievement_enabled;
            $labProgram->save();

            return $labProgram;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getLabProgramListName($request, $organization)
    {
        try {
            $labProgramList = LabProgram::select('uuid', 'title', 'media')->where(['organization_id' => $organization->id, 'is_accessible' => '1']);
            $labProgramList = self::filterLabProgramList($labProgramList, $request);
            $limit = config('site-settings.listing_limit');

            return $labProgramList->limit($limit)->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabProgramBasedOnUUID($uUID)
    {
        try {
            return LabProgram::select('id', 'uuid', 'title', 'media', 'slug', 'description')->where('UUID', $uUID)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabProgramBasedOnId($Id)
    {
        try {
            return LabProgram::select('id', 'uuid', 'title', 'media', 'slug', 'description')->where(['id' => $Id, 'is_accessible' => '1'])->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabProgramIdBasedOnUUIDArray($uuid)
    {
        try {
            $labProgram = LabProgram::whereIn('uuid', $uuid)->pluck('id')->all();
            if ($labProgram != null) {
                return $labProgram;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteOrganizationLabProgram($organizationId)
    {
        try {
            $fetchOrganizationLabPrograms = LabProgram::where('organization_id', $organizationId)->get();
            if (!empty($fetchOrganizationLabPrograms)) {
                foreach ($fetchOrganizationLabPrograms as $organizationLabProgram) {
                    $deleteOrganizationLabProgram = self::delete($organizationLabProgram->slug);
                    if (!$deleteOrganizationLabProgram) {
                        return false;
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabProgramTitleBasedOnUUIDArray($uuid)
    {
        try {
            $labProgram = LabProgram::whereIn('uuid', $uuid)->pluck('title')->all();
            if ($labProgram != null) {
                return $labProgram;
            }

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function fetchLabProgramReportBasedOnOrganization($organizationId)
    {
        try {
            $fetchLabProgram = LabProgram::where(['organization_id' => $organizationId, 'status' => '1', 'is_accessible' => '1'])->get();

            return $fetchLabProgram;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
