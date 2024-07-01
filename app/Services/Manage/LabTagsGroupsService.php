<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\LabTagsGroups;

class LabTagsGroupsService
{
    public function createLabTagsGroups($request, $lab)
    {
        if ($request->has('tags')) {
            if (count($request->tags) > 0) {
                foreach ($request->tags as $tag) {
                    $LabSkillsGroupsStack = new LabTagsGroups();
                    $LabSkillsGroupsStack->lab_id = $lab->id;
                    $LabSkillsGroupsStack->foreign_id = $tag;
                    $LabSkillsGroupsStack->type = '0';
                    $LabSkillsGroupsStack->save();
                }
            }
        }
        if ($request->has('tag_groups')) {
            if (count($request->tag_groups) > 0) {
                foreach ($request->tag_groups as $tag_group) {
                    $LabSkillsGroupsStack = new LabTagsGroups();
                    $LabSkillsGroupsStack->lab_id = $lab->id;
                    $LabSkillsGroupsStack->foreign_id = $tag_group;
                    $LabSkillsGroupsStack->type = '1';
                    $LabSkillsGroupsStack->save();
                }
            }
        }

        return true;
    }

    public function updateLabTagsGroups($request, $lab_id)
    {
        try {
            if ($request->has('tags')) {
                if (count($request->tags) > 0) {
                    $getExistsLabTags = LabTagsGroups::where([
                        ['lab_id', '=', $lab_id],
                        ['type', '=', '0'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsLabTags, $request->tags);
                    $deleteNonExisting = LabTagsGroups::where([
                        ['lab_id', '=', $lab_id],
                        ['type', '=', '0'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newTags = array_diff($request->tags, $getExistsLabTags);
                    foreach ($newTags as $tag) {
                        $LabSkillsGroupsStack = new LabTagsGroups();
                        $LabSkillsGroupsStack->lab_id = $lab_id;
                        $LabSkillsGroupsStack->foreign_id = $tag;
                        $LabSkillsGroupsStack->type = '0';
                        $LabSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('tag_groups')) {
                if (count($request->tag_groups) > 0) {
                    $getExistsLabTagsGroups = LabTagsGroups::where([
                        ['lab_id', '=', $lab_id],
                        ['type', '=', '1'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsLabTagsGroups, $request->tag_groups);
                    $deleteNonExisting = LabTagsGroups::where([
                        ['lab_id', '=', $lab_id],
                        ['type', '=', '1'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newTagsGroups = array_diff($request->tag_groups, $getExistsLabTagsGroups);
                    foreach ($newTagsGroups as $tag_group) {
                        $LabSkillsGroupsStack = new LabTagsGroups();
                        $LabSkillsGroupsStack->lab_id = $lab_id;
                        $LabSkillsGroupsStack->foreign_id = $tag_group;
                        $LabSkillsGroupsStack->type = '1';
                        $LabSkillsGroupsStack->save();
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteLabTagsGroups($lab_id)
    {
        try {
            $labTagsGroups = LabTagsGroups::select('id')->where('lab_id', $lab_id)->get()->toArray();
            if ($labTagsGroups) {
                $deleteLabTagsGroups = labTagsGroups::whereIn('id', $labTagsGroups)->delete();
                if (!$deleteLabTagsGroups) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabsIdBasedOnTagsId($tagIds)
    {
        try {
            if (count($tagIds) > 0) {
                $getTags = LabTagsGroups::whereIn('foreign_id', $tagIds)
                    ->where('type', '0')
                    ->pluck('lab_id');
            } else {
                $getTags = LabTagsGroups::where('type', '0')
                    ->pluck('lab_id');
                if (count($getTags) > 0) {
                    $getTags = $getTags->random();
                }
            }

            return $getTags;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
