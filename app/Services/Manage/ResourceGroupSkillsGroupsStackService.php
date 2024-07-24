<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
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
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateResourceGroupSkillsGroupsStack($request, $updateResourceGroupId)
    {
        try {
            if ($request->has('skills')) {
                if (count($request->skills) > 0) {
                    $getExistsSkills = ResourceGroupSkillsGroupStack::where([
                        ['resource_group_id', '=', $updateResourceGroupId],
                        ['type', '=', '0'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsSkills, $request->skills);
                    $deleteNonExistingSkills = ResourceGroupSkillsGroupStack::where([
                        ['resource_group_id', '=', $updateResourceGroupId],
                        ['type', '=', '0'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkills = array_diff($request->skills, $getExistsSkills);
                    foreach ($newSkills as $skill) {
                        $ResourceGroupSkillsGroupsStack = new ResourceGroupSkillsGroupStack();
                        $ResourceGroupSkillsGroupsStack->resource_group_id = $updateResourceGroupId;
                        $ResourceGroupSkillsGroupsStack->foreign_id = $skill;
                        $ResourceGroupSkillsGroupsStack->type = '0';
                        $ResourceGroupSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('skill_groups')) {
                if (count($request->skill_groups) > 0) {
                    $getExistsSkillsGroup = ResourceGroupSkillsGroupStack::where([
                        ['resource_group_id', '=', $updateResourceGroupId],
                        ['type', '=', '1'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsSkillsGroup, $request->skill_groups);
                    $deleteNonExistingSkillsGroup = ResourceGroupSkillsGroupStack::where([
                        ['resource_group_id', '=', $updateResourceGroupId],
                        ['type', '=', '1'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkillGroup = array_diff($request->skill_groups, $getExistsSkillsGroup);
                    foreach ($newSkillGroup as $skill_group) {
                        $ResourceGroupSkillsGroupsStack = new ResourceGroupSkillsGroupStack();
                        $ResourceGroupSkillsGroupsStack->resource_group_id = $updateResourceGroupId;
                        $ResourceGroupSkillsGroupsStack->foreign_id = $skill_group;
                        $ResourceGroupSkillsGroupsStack->type = '1';
                        $ResourceGroupSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('skill_stacks')) {
                if (count($request->skill_stacks) > 0) {
                    $getExistsSkillStack = ResourceGroupSkillsGroupStack::where([
                        ['resource_group_id', '=', $updateResourceGroupId],
                        ['type', '=', '2'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsSkillStack, $request->skill_stacks);
                    $deleteNonExistingSkillStack = ResourceGroupSkillsGroupStack::where([
                        ['resource_group_id', '=', $updateResourceGroupId],
                        ['type', '=', '2'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkillStack = array_diff($request->skill_stacks, $getExistsSkillStack);
                    foreach ($newSkillStack as $skill_stack) {
                        $ResourceGroupSkillsGroupsStack = new ResourceGroupSkillsGroupStack();
                        $ResourceGroupSkillsGroupsStack->resource_group_id = $updateResourceGroupId;
                        $ResourceGroupSkillsGroupsStack->foreign_id = $skill_stack;
                        $ResourceGroupSkillsGroupsStack->type = '2';
                        $ResourceGroupSkillsGroupsStack->save();
                    }
                }
            }
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getRecommendedSkills($skills)
    {
        try {
            $resourceGroupSkillIds = [];
            $resourceGroupId = ResourceGroupSkillsGroupStack::where('type', '0')
                ->whereIn('foreign_id', $skills)->pluck('resource_group_id')->unique();
            if (!empty($resourceGroupId)) {
                $resourceGroupSkillIds = ResourceGroupSkillsGroupStack::where('type', '0')
                    ->whereIn('resource_group_id', $resourceGroupId)->pluck('foreign_id');
            }

            return $resourceGroupSkillIds;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function cloneResourceGroupSkillsGroupsStack($originalResourceGroupAssociation, $clonedResourceGroupId)
    {
        try {
            $originalResourceGroupAssociation->each(function ($resource_group_skill_group) use ($clonedResourceGroupId) {
                if ($resource_group_skill_group) {
                    $cloneResourceGroupSKills = $resource_group_skill_group->replicate();
                    $cloneResourceGroupSKills->resource_group_id = $clonedResourceGroupId;
                    $cloneResourceGroupSKills->save();
                }
            });

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
