<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ResourceModuleSkillsGroupsStack;

class ResourceModuleSkillsGroupsStackService
{
    public function createResourceModuleSkillsGroupsStack($request, $resource_module_id)
    {
        if ($request->has('skills')) {
            if (count($request->skills) > 0) {
                foreach ($request->skills as $skill) {
                    $ResourceModuleGroupsStack = new ResourceModuleSkillsGroupsStack();
                    $ResourceModuleGroupsStack->resource_module_id = $resource_module_id;
                    $ResourceModuleGroupsStack->foreign_id = $skill;
                    $ResourceModuleGroupsStack->type = '0';
                    $ResourceModuleGroupsStack->save();
                }
            }
        }
        if ($request->has('skill_groups')) {
            if (count($request->skill_groups) > 0) {
                foreach ($request->skill_groups as $skill_group) {
                    $ResourceModuleGroupsStack = new ResourceModuleSkillsGroupsStack();
                    $ResourceModuleGroupsStack->resource_module_id = $resource_module_id;
                    $ResourceModuleGroupsStack->foreign_id = $skill_group;
                    $ResourceModuleGroupsStack->type = '1';
                    $ResourceModuleGroupsStack->save();
                }
            }
        }
        if ($request->has('skill_stacks')) {
            if (count($request->skill_stacks) > 0) {
                foreach ($request->skill_stacks as $skill_stack) {
                    $ResourceModuleGroupsStack = new ResourceModuleSkillsGroupsStack();
                    $ResourceModuleGroupsStack->resource_module_id = $resource_module_id;
                    $ResourceModuleGroupsStack->foreign_id = $skill_stack;
                    $ResourceModuleGroupsStack->type = '2';
                    $ResourceModuleGroupsStack->save();
                }
            }
        }

        return true;
    }

    public function updateResourceModuleSkillsGroupsStack($request, $resource_module_id)
    {
        try {
            if ($request->has('skills')) {
                if (count($request->skills) > 0) {
                    $getExistsSkills = ResourceModuleSkillsGroupsStack::where([
                        ['resource_module_id', '=', $resource_module_id],
                        ['type', '=', '0'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsSkills, $request->skills);
                    $deleteNonExistingSkills = ResourceModuleSkillsGroupsStack::where([
                        ['resource_module_id', '=', $resource_module_id],
                        ['type', '=', '0'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkills = array_diff($request->skills, $getExistsSkills);
                    foreach ($newSkills as $skill) {
                        $ResourceModuleSkillsGroupsStack = new ResourceModuleSkillsGroupsStack();
                        $ResourceModuleSkillsGroupsStack->resource_module_id = $resource_module_id;
                        $ResourceModuleSkillsGroupsStack->foreign_id = $skill;
                        $ResourceModuleSkillsGroupsStack->type = '0';
                        $ResourceModuleSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('skill_groups')) {
                if (count($request->skill_groups) > 0) {
                    $getExistsSkillsGroup = ResourceModuleSkillsGroupsStack::where([
                        ['resource_module_id', '=', $resource_module_id],
                        ['type', '=', '1'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsSkillsGroup, $request->skill_groups);
                    $deleteNonExistingSkillsGroup = ResourceModuleSkillsGroupsStack::where([
                        ['resource_module_id', '=', $resource_module_id],
                        ['type', '=', '1'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkillGroup = array_diff($request->skill_groups, $getExistsSkillsGroup);
                    foreach ($newSkillGroup as $skill_group) {
                        $ResourceModuleSkillsGroupsStack = new ResourceModuleSkillsGroupsStack();
                        $ResourceModuleSkillsGroupsStack->resource_module_id = $resource_module_id;
                        $ResourceModuleSkillsGroupsStack->foreign_id = $skill_group;
                        $ResourceModuleSkillsGroupsStack->type = '1';
                        $ResourceModuleSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('skill_stacks')) {
                if (count($request->skill_stacks) > 0) {
                    $getExistsSkillStack = ResourceModuleSkillsGroupsStack::where([
                        ['resource_module_id', '=', $resource_module_id],
                        ['type', '=', '2'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsSkillStack, $request->skill_stacks);
                    $deleteNonExistingSkillStack = ResourceModuleSkillsGroupsStack::where([
                        ['resource_module_id', '=', $resource_module_id],
                        ['type', '=', '2'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkillStack = array_diff($request->skill_stacks, $getExistsSkillStack);
                    foreach ($newSkillStack as $skill_stack) {
                        $ResourceModuleSkillsGroupsStack = new ResourceModuleSkillsGroupsStack();
                        $ResourceModuleSkillsGroupsStack->resource_module_id = $resource_module_id;
                        $ResourceModuleSkillsGroupsStack->foreign_id = $skill_stack;
                        $ResourceModuleSkillsGroupsStack->type = '2';
                        $ResourceModuleSkillsGroupsStack->save();
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteResourceModuleSkillsGroupsStack($resource_module_id)
    {
        try {
            ResourceModuleSkillsGroupsStack::where('resource_module_id', $resource_module_id)->delete();

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getRecommendedSkills($skills)
    {
        try {
            $resourceSkillIds = [];
            $resourceModuleIds = ResourceModuleSkillsGroupsStack::where('type', '0')
                ->whereIn('foreign_id', $skills)->pluck('resource_module_id');
            if (!empty($resourceModuleIds)) {
                $resourceSkillIds = ResourceModuleSkillsGroupsStack::where('type', '0')
                    ->whereIn('resource_module_id', $resourceModuleIds)->pluck('foreign_id')->unique();
            }

            return $resourceSkillIds;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getSkillsBasedOnResourceModule($resourceModuleId)
    {
        try {
            $data = ResourceModuleSkillsGroupsStack::where('type', '0')
                ->where('resource_module_id', $resourceModuleId)
                ->pluck('foreign_id')
                ->unique();

            return $data;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function cloneResourceModuleSkillsGroupsStack($originalResourceModuleAssociation, $clonedResourceModuleId)
    {
        try {
            $originalResourceModuleAssociation->each(function ($resource_module_skill_group) use ($clonedResourceModuleId) {
                if ($resource_module_skill_group) {
                    $cloneResourceModuleSKills = $resource_module_skill_group->replicate();
                    $cloneResourceModuleSKills->resource_module_id = $clonedResourceModuleId;
                    $cloneResourceModuleSKills->save();
                }
            });

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
