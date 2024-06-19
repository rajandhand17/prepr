<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Models\Skill;
use App\Services\Manage\ChallengeSkillsGroupsStackService;
use App\Services\Manage\ResourceCollectionSkillsGroupsStackService;
use App\Services\Manage\ResourceGroupSkillsGroupsStackService;
use App\Services\Manage\ResourceModuleSkillsGroupsStackService;
use DB;
use Illuminate\Support\Facades\Schema;

class SkillService
{
    public static function getSkills($language = 'en', $search = null, $sortBy = null, $skill_id, $pagination = null)
    {
        try {
            if ($language == 'en') {
                $skill_list = Skill::select('id', 'title');
                if ($skill_id !== null) {
                    if (gettype($skill_id) == 'string') {
                        $skill_list = $skill_list->where('id', $skill_id);
                    } else {
                        $skill_list = $skill_list->whereIn('id', $skill_id->toArray());
                        if ($sortBy == null) {
                            $skill_list = $skill_list->orderByRaw('FIELD(id, '.$skill_id->implode(',').')');
                        }
                    }
                }
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('skills', $column_name)) {
                    return false;
                }
                $skill_list = Skill::select('id', $column_name.' as title');
            }

            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $skill_list = self::filterSkillList($skill_list, $column_name, $search);
            }

            if ($sortBy !== null) {
                switch ($sortBy) {
                    case 'name-a-to-z':
                        $skill_list = $skill_list->orderBy('skills.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $skill_list = $skill_list->orderBy('skills.title', 'DESC');
                        break;
                    case 'creation_date':
                        $skill_list = $skill_list->orderBy('skills.created_at', 'ASC');
                        break;
                    default:
                        $skill_list = $skill_list->orderBy('skills.id', 'ASC');
                }
            }
            //take 20 results based from the table
            $skill_list = $skill_list->take(config('site-settings.dropdown_listing_limit'));
            if (auth()->user() || $pagination == true) {
                $skill_list = $skill_list->paginate(config('site-settings.pagination_per_page'));
            } else {
                $skill_list = $skill_list->get();
            }
            if (!$skill_list->isEmpty()) {
                return $skill_list;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function filterSKillList($getSkillsList, $sKill_column_name, $search)
    {
        try {
            $getSkillsList = $getSkillsList->where($sKill_column_name, 'like', '%'.$search.'%');
            if ($getSkillsList) {
                return $getSkillsList;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getSkillBasedOnIds($skill_ids)
    {
        try {
            $getSkillsList = Skill::select('id', LanguageColumnHelper::getLanguageColumnName(app()->getLocale(), 'title').' as title')
                ->whereIn('id', $skill_ids)->get();
            if ($getSkillsList) {
                return $getSkillsList;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getSkillBasedOnId($skill_id)
    {
        try {
            $getSkillsList = Skill::select('id', LanguageColumnHelper::getLanguageColumnName(app()->getLocale(), 'title').' as title')
                ->where('id', $skill_id)->first();
            if ($getSkillsList) {
                return $getSkillsList;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function recommendSkills($getUserSkills)
    {
        try {
            // Get challenge skills
            $challengeSkills = ChallengeSkillsGroupsStackService::getRecommendedSkills($getUserSkills);
            // Get resource module skills
            $resourceModuleSkills = ResourceModuleSkillsGroupsStackService::getRecommendedSkills($getUserSkills);
            // Get resource collection skills
            $resourceCollectionSkills = ResourceCollectionSkillsGroupsStackService::getRecommendedSkills($getUserSkills);
            // Get resource group skills
            $resourceGroupSkills = ResourceGroupSkillsGroupsStackService::getRecommendedSkills($getUserSkills);
            // Merge all skill IDs
            $mergedSkills = $challengeSkills->merge($resourceModuleSkills)
                ->merge($resourceCollectionSkills)
                ->merge($resourceGroupSkills);
            // Make the merged skills unique
            $uniqueSkills = $mergedSkills->unique()->diff($getUserSkills);
            //Fetch the 12 recommendations skills
            $skills = Skill::select('id', 'title')->whereIn('id', $uniqueSkills)->take(config('site-settings.explore_page_limit_max'))->get();

            return $skills;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getCurrentUsersMatchedSkills($skills)
    {
        try {
            $getCurrentUsersSkills = UserSkillsService::getUserSkills();
            if ($getCurrentUsersSkills) {
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function createSkillFromGO1($skills)
    {
        return array_map(function ($item) {
            $data = Skill::firstOrCreate(['title' => $item['name']]);

            return $data->id;
        }, $skills);
    }
}
