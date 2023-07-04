<?php

namespace App\Services;

use App\Models\labTagsGroups;

class LabTagsGroupsService
{
    public function createLabTagsGroups($request, $lab)
    {
        try {
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
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteLabTagsGroups($lab_id){
       try {
        $labTagsGroups=LabTagsGroups::select('id')->where('lab_id',$lab_id)->get()->toArray();
        if($labTagsGroups){
            $deleteLabTagsGroups=labTagsGroups::whereIn('id',$labTagsGroups)->delete();
            if(!$deleteLabTagsGroups){
                return false;
            }
        }
        return true;
       }catch (\Exception $e) {
        return false;
       }
    }
}
