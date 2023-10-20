<?php

namespace App\Services\Manage;


use App\Models\ResourceCollectionSkillsGroupsStack;

class ResourceCollectionSkillsGroupsStackService
{
    public function createResourceCollectionSkillsGroupsStack($request, $resource_collection_id)
    {
        if ($request->has('skills')) {
            if (count($request->skills) > 0) {
                foreach ($request->skills as $skill) {
                    $resourceCollectionGroupsSkill=new ResourceCollectionSkillsGroupsStack();
                    $resourceCollectionGroupsSkill->resource_collection_id = $resource_collection_id;
                    $resourceCollectionGroupsSkill->foreign_id = $skill;
                    $resourceCollectionGroupsSkill->type = '0';
                    $resourceCollectionGroupsSkill->save();
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
                    $ResourceCollectionGroupsStack = new ResourceCollectionSkillsGroupsStack();
                    $ResourceCollectionGroupsStack->resource_collection_id = $resource_collection_id;
                    $ResourceCollectionGroupsStack->foreign_id = $skill_stack;
                    $ResourceCollectionGroupsStack->type = '2';
                    $ResourceCollectionGroupsStack->save();
                }
            }
        }

        return true;
    }
}
