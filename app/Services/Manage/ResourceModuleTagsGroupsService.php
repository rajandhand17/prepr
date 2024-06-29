<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ResourceModuleTagsGroups;

class ResourceModuleTagsGroupsService
{
    public function createResourceModuleTagsGroups($request, $resource_module_id)
    {
        if ($request->has('tags')) {
            if (count($request->tags) > 0) {
                foreach ($request->tags as $tag) {
                    $labSkillsGroupsStack = new ResourceModuleTagsGroups();
                    $labSkillsGroupsStack->resource_module_id = $resource_module_id;
                    $labSkillsGroupsStack->foreign_id = $tag;
                    $labSkillsGroupsStack->type = '0';
                    $labSkillsGroupsStack->save();
                }
            }
        }
        if ($request->has('tag_groups')) {
            if (count($request->tag_groups) > 0) {
                foreach ($request->tag_groups as $tag_group) {
                    $labSkillsGroupsStack = new ResourceModuleTagsGroups();
                    $labSkillsGroupsStack->resource_module_id = $resource_module_id;
                    $labSkillsGroupsStack->foreign_id = $tag_group;
                    $labSkillsGroupsStack->type = '1';
                    $labSkillsGroupsStack->save();
                }
            }
        }

        return true;
    }

    public static function delete($resource_module_id)
    {
        try {
            $resourceModuleSkillsGroupsStack = ResourceModuleTagsGroups::where('resource_module_id', $resource_module_id)->first();
            if ($resourceModuleSkillsGroupsStack !== null) {
                return $resourceModuleSkillsGroupsStack->delete();
            }

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function updateResourceModuleTagsGroups($request, $resource_module_id)
    {
        try {
            if ($request->has('tags')) {
                if (count($request->tags) > 0) {
                    $getExistsResourceModuleTags = ResourceModuleTagsGroups::where([
                        ['resource_module_id', '=', $resource_module_id],
                        ['type', '=', '0'],
                    ])->pluck('foreign_id')->all();
                    $nonExistingIds = array_diff($getExistsResourceModuleTags, $request->tags);
                    $deleteNonExisting = ResourceModuleTagsGroups::where([
                        ['resource_module_id', '=', $resource_module_id],
                        ['type', '=', '0'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newTags = array_diff($request->tags, $getExistsResourceModuleTags);
                    foreach ($newTags as $tag) {
                        $ResourceModuleSkillsGroupsStack = new ResourceModuleTagsGroups();
                        $ResourceModuleSkillsGroupsStack->resource_module_id = $resource_module_id;
                        $ResourceModuleSkillsGroupsStack->foreign_id = $tag;
                        $ResourceModuleSkillsGroupsStack->type = '0';
                        $ResourceModuleSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('tag_groups')) {
                if (count($request->tag_groups) > 0) {
                    $getExistsResourceModuleTagsGroups = ResourceModuleTagsGroups::where([
                        ['resource_module_id', '=', $resource_module_id],
                        ['type', '=', '1'],
                    ])->pluck('foreign_id')->all();
                    $nonExistingIds = array_diff($getExistsResourceModuleTagsGroups, $request->tag_groups);
                    $deleteNonExisting = ResourceModuleTagsGroups::where([
                        ['resource_module_id', '=', $resource_module_id],
                        ['type', '=', '1'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newTagsGroups = array_diff($request->tag_groups, $getExistsResourceModuleTagsGroups);
                    foreach ($newTagsGroups as $tag_group) {
                        $ResourceModuleSkillsGroupsStack = new ResourceModuleTagsGroups();
                        $ResourceModuleSkillsGroupsStack->resource_module_id = $resource_module_id;
                        $ResourceModuleSkillsGroupsStack->foreign_id = $tag_group;
                        $ResourceModuleSkillsGroupsStack->type = '1';
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

    public static function deleteResourceModuleTagsGroups($resource_module_id)
    {
        try {
            ResourceModuleTagsGroups::where('resource_module_id', $resource_module_id)->delete();

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
