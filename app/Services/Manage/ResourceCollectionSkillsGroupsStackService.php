<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ResourceCollectionSkillsGroupsStack;

class ResourceCollectionSkillsGroupsStackService
{
    public function createResourceCollectionSkillsGroupsStack($request, $resource_collection_id)
    {
        try {
            if ($request->has('skills')) {
                if (count($request->skills) > 0) {
                    foreach ($request->skills as $skill) {
                        $resourceCollectionSkill = new ResourceCollectionSkillsGroupsStack();
                        $resourceCollectionSkill->resource_collection_id = $resource_collection_id;
                        $resourceCollectionSkill->foreign_id = $skill;
                        $resourceCollectionSkill->type = '0';
                        $resourceCollectionSkill->save();
                    }
                }
            }
            if ($request->has('skill_groups')) {
                if (count($request->skill_groups) > 0) {
                    foreach ($request->skill_groups as $skill_group) {
                        $ResourceCollectionSkillGroups = new ResourceCollectionSkillsGroupsStack();
                        $ResourceCollectionSkillGroups->resource_collection_id = $resource_collection_id;
                        $ResourceCollectionSkillGroups->foreign_id = $skill_group;
                        $ResourceCollectionSkillGroups->type = '1';
                        $ResourceCollectionSkillGroups->save();
                    }
                }
            }
            if ($request->has('skill_stacks')) {
                if (count($request->skill_stacks) > 0) {
                    foreach ($request->skill_stacks as $skill_stack) {
                        $ResourceCollectionSkillStack = new ResourceCollectionSkillsGroupsStack();
                        $ResourceCollectionSkillStack->resource_collection_id = $resource_collection_id;
                        $ResourceCollectionSkillStack->foreign_id = $skill_stack;
                        $ResourceCollectionSkillStack->type = '2';
                        $ResourceCollectionSkillStack->save();
                    }
                }
            }

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateResourceCollectionSkillsGroupsStack($request, $resource_collection_id)
    {
        try {
            if ($request->has('skills')) {
                if (count($request->skills) > 0) {
                    $getExistsSkills = ResourceCollectionSkillsGroupsStack::where([
                        ['resource_collection_id', '=', $resource_collection_id],
                        ['type', '=', '0'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsSkills, $request->skills);
                    $deleteNonExistingSkills = ResourceCollectionSkillsGroupsStack::where([
                        ['resource_collection_id', '=', $resource_collection_id],
                        ['type', '=', '0'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkills = array_diff($request->skills, $getExistsSkills);
                    foreach ($newSkills as $skill) {
                        $resourceCollectionSkill = new ResourceCollectionSkillsGroupsStack();
                        $resourceCollectionSkill->resource_collection_id = $resource_collection_id;
                        $resourceCollectionSkill->foreign_id = $skill;
                        $resourceCollectionSkill->type = '0';
                        $resourceCollectionSkill->save();
                    }
                }
            }
            if ($request->has('skill_groups')) {
                if (count($request->skill_groups) > 0) {
                    $getExistsSkillGroups = ResourceCollectionSkillsGroupsStack::where([
                        ['resource_collection_id', '=', $resource_collection_id],
                        ['type', '=', '1'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsSkillGroups, $request->skill_groups);
                    $deleteNonExistingSkills = ResourceCollectionSkillsGroupsStack::where([
                        ['resource_collection_id', '=', $resource_collection_id],
                        ['type', '=', '1'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkillGroups = array_diff($request->skill_groups, $getExistsSkillGroups);
                    foreach ($newSkillGroups as $skill_group) {
                        $ResourceCollectionSkillGroups = new ResourceCollectionSkillsGroupsStack();
                        $ResourceCollectionSkillGroups->resource_collection_id = $resource_collection_id;
                        $ResourceCollectionSkillGroups->foreign_id = $skill_group;
                        $ResourceCollectionSkillGroups->type = '1';
                        $ResourceCollectionSkillGroups->save();
                    }
                }
            }
            if ($request->has('skill_stacks')) {
                if (count($request->skill_stacks) > 0) {
                    $getExistsSkillStacks = ResourceCollectionSkillsGroupsStack::where([
                        ['resource_collection_id', '=', $resource_collection_id],
                        ['type', '=', '2'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsSkillStacks, $request->skill_stacks);
                    $deleteNonExistingSkills = ResourceCollectionSkillsGroupsStack::where([
                        ['resource_collection_id', '=', $resource_collection_id],
                        ['type', '=', '2'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkillGroups = array_diff($request->skill_stacks, $getExistsSkillStacks);
                    foreach ($newSkillGroups as $skill_stack) {
                        $ResourceCollectionSkillStack = new ResourceCollectionSkillsGroupsStack();
                        $ResourceCollectionSkillStack->resource_collection_id = $resource_collection_id;
                        $ResourceCollectionSkillStack->foreign_id = $skill_stack;
                        $ResourceCollectionSkillStack->type = '2';
                        $ResourceCollectionSkillStack->save();
                    }
                }
            }

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteResourceCollectionSkillsGroupsStack($resource_collection_id)
    {
        try {
            $checkExistsResourceCollectionSkillsGroupsStack = ResourceCollectionSkillsGroupsStack::select('id')->where('resource_collection_id', $resource_collection_id)->pluck('id');
            if ($checkExistsResourceCollectionSkillsGroupsStack) {
                $deleteResourceCollectionSkillsGroupsStack = ResourceCollectionSkillsGroupsStack::whereIn('id', $checkExistsResourceCollectionSkillsGroupsStack)->delete();
                if (!$deleteResourceCollectionSkillsGroupsStack) {
                    return false;
                }
            }

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getRecommendedSkills($skills)
    {
        try {
            $resourceSkillIds = [];
            $resourceCollectionId = ResourceCollectionSkillsGroupsStack::where('type', '0')
                ->whereIn('foreign_id', $skills)->pluck('resource_collection_id')->unique();
            if (!empty($resourceCollectionId)) {
                $resourceSkillIds = ResourceCollectionSkillsGroupsStack::where('type', '0')
                    ->whereIn('resource_collection_id', $resourceCollectionId)->pluck('foreign_id');
            }

            return $resourceSkillIds;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
