<?php

namespace App\Services\Manage;

use App\Models\LabSkillsGroupsStack;
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
            return false;
        }
    }

    public static function deleteResourceCollectionSkillsGroupsStack($resource_collection_id){
        try {
            $checkExistsResourceCollectionSkillsGroupsStack = ResourceCollectionSkillsGroupsStack::select('id')->where('resource_collection_id', $resource_collection_id)->get()->toArray();
            if ($checkExistsResourceCollectionSkillsGroupsStack) {
                $deleteResourceCollectionSkillsGroupsStack = ResourceCollectionSkillsGroupsStack::whereIn('id', $checkExistsResourceCollectionSkillsGroupsStack)->delete();
                if (!$deleteResourceCollectionSkillsGroupsStack) {
                    return false;
                }
            }
            return true;
        }catch(\Exception $e) {
            return false;
        }
    }
}
