<?php

namespace App\Services\Manage;

use App\Models\ResourceGroupSkillsGroupStack;

class ResourceGroupSkillsGroupsStackService
{
    public static function createResourceGroupSkillsGroupsStack($request, $resource_group_id)
    {
        try {
            if ($request->has('skills')) {
                if (count($request->skills) > 0) {
                    foreach ($request->skills as $skill) {
                        $resourceGroupSkill = new ResourceGroupSkillsGroupStack();
                        $resourceGroupSkill->resource_group_id = $resource_group_id;
                        $resourceGroupSkill->foreign_id = $skill;
                        $resourceGroupSkill->type = '0';
                        $resourceGroupSkill->save();
                    }
                }
            }
            if ($request->has('skill_groups')) {
                if (count($request->skill_groups) > 0) {
                    foreach ($request->skill_groups as $skill_group) {
                        $ResourceGroupSkillGroups = new ResourceGroupSkillsGroupStack();
                        $ResourceGroupSkillGroups->resource_group_id = $resource_group_id;
                        $ResourceGroupSkillGroups->foreign_id = $skill_group;
                        $ResourceGroupSkillGroups->type = '1';
                        $ResourceGroupSkillGroups->save();
                    }
                }
            }
            if ($request->has('skill_stacks')) {
                if (count($request->skill_stacks) > 0) {
                    foreach ($request->skill_stacks as $skill_stack) {
                        $ResourceCollectionSkillStack = new ResourceGroupSkillsGroupStack();
                        $ResourceCollectionSkillStack->resource_group_id = $resource_group_id;
                        $ResourceCollectionSkillStack->foreign_id = $skill_stack;
                        $ResourceCollectionSkillStack->type = '2';
                        $ResourceCollectionSkillStack->save();
                    }
                }
            }

            return true;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function deleteResourceGroupSkillsGroupsStack($resource_group_id)
    {
        try {
            $checkExistsResourceGroupSkillsGroupsStack = ResourceGroupSkillsGroupStack::select('id')->where('resource_group_id', $resource_group_id)->pluck('id');
            if ($checkExistsResourceGroupSkillsGroupsStack) {
                $deleteResourceGroupSkillsGroupsStack = ResourceGroupSkillsGroupStack::whereIn('id', $checkExistsResourceGroupSkillsGroupsStack)->delete();
                if (!$deleteResourceGroupSkillsGroupsStack) {
                    return false;
                }
            }

            return true;
        } catch(\Exception $e) {
            return false;
        }
    }
}
