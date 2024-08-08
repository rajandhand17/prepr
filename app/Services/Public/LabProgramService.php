<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\LabProgram;
use App\Services\Manage\LabProgramTypeModesService;

class LabProgramService
{
    public function getList($request)
    {
        try {
            $labProgramList = LabProgram::where('is_accessible', '1');
            $labProgramList = self::filterLabProgramList($request, $labProgramList);

            return $labProgramList->paginate(config('site-settings.pagination_per_page'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function filterLabProgramList($request, $labProgramList)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
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
            if ($request->has('organization_id') && !empty($request->organization_id)) {
                $getOrganizationIds = OrganizationService::getOrganizationExistBasedOnUuidArray($request->organization_id)->pluck('id');
                $labProgramList = $labProgramList->whereIn('organization_id', $getOrganizationIds);
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

            return $labProgramList;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getLabProgramBasedOnSlug($slug)
    {
        try {
            return LabProgram::where('slug', $slug)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabProgramBasedOnId($id)
    {
        try {
            return LabProgram::where(['id' => $id, 'is_accessible' => '1'])->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getComponentBasedLabProgramList($request, $organizationId)
    {
        try {
            $lab_program_list = LabProgram::where(['lab_programs.organization_id' => $organizationId, 'lab_programs.status' => '1', 'lab_programs.is_accessible' => '1']);
            $lab_program_list = self::filterLabProgramList($request, $lab_program_list);

            return $lab_program_list->paginate(config('site-settings.association_pagination_per_page'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchLabProgramAssociation($request, $fetchLabProgramIdsAssociatedLabId)
    {
        try {
            $lab_program_list = LabProgram::whereIn('lab_programs.id', $fetchLabProgramIdsAssociatedLabId)->where(['lab_programs.status' => '1', 'lab_programs.is_accessible' => '1']);
            $lab_program_list = self::filterLabProgramList($request, $lab_program_list);

            return $lab_program_list->paginate(config('site-settings.association_pagination_per_page'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getRelatedLabPrograms($labProgramIds)
    {
        try {
            // Retrieve lab program with the given IDs using findMany for primary keys and limiting by 2 values
            $labPrograms = LabProgram::findMany($labProgramIds)->slice(0, 2);

            return $labPrograms;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
