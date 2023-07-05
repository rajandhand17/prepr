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
    public function updateLabTagsGroups($lab_id,$request)
    {
        try {
            if ($request->has('tags')) {
                if (count($request->tags) > 0) {
                    $getExistsLabSkillsGroupsStack=LabTagsGroups::select('id','foreign_id')->where([
                        ['lab_id', '=', $lab_id],
                        ['type', '=', '0'],
                    ])->get();
                    foreach($getExistsLabSkillsGroupsStack as $key=>$existsSkillsGroupsStack){
                        $existsInArray=in_array($existsSkillsGroupsStack['foreign_id'],$request->tags);
                        if(!$existsInArray){
                            $deleteLabSkillsGroupsStack=LabTagsGroups::where('id',$existsSkillsGroupsStack['id'])->delete();
                        }
                    }
                    foreach ($request->tags as $tag) {
                        $checkExistSkills=LabTagsGroups::select('id')->where([
                            ['lab_id', '=', $lab_id],
                            ['type', '=', '0'],
                            ['foreign_id', '=', $tag],
                        ])->first();
                        if(!$checkExistSkills){
                        $LabSkillsGroupsStack = new LabTagsGroups();
                        $LabSkillsGroupsStack->lab_id = $lab_id;
                        $LabSkillsGroupsStack->foreign_id = $tag;
                        $LabSkillsGroupsStack->type = '0';
                        $LabSkillsGroupsStack->save();
                        }
                    }
                }
            }
            if ($request->has('tag_groups')) {
                
                if (count($request->tag_groups) > 0) {
                    $getExistsLabSkillsTagGroups=LabTagsGroups::select('id','foreign_id')->where([
                        ['lab_id', '=', $lab_id],
                        ['type', '=', '1'],
                    ])->get();
                    foreach($getExistsLabSkillsTagGroups as $key=>$existsSkillsTagGroups){
                        $existsInTagGroupsArray=in_array($existsSkillsTagGroups['foreign_id'],$request->tag_groups);
                        if(!$existsInTagGroupsArray){
                            $deleteLabSkillsGroupsStack=LabTagsGroups::where('id',$existsSkillsTagGroups['id'])->delete();
                        }
                    }
                    foreach ($request->tag_groups as $tag_group) {
                        $checkExistSkills=LabTagsGroups::select('id')->where([
                            ['lab_id', '=', $lab_id],
                            ['type', '=', '1'],
                            ['foreign_id', '=', $tag_group],
                        ])->first();
                        if(!$checkExistSkills){
                        $LabSkillsGroupsStack = new LabTagsGroups();
                        $LabSkillsGroupsStack->lab_id = $lab_id;
                        $LabSkillsGroupsStack->foreign_id = $tag_group;
                        $LabSkillsGroupsStack->type = '1';
                        $LabSkillsGroupsStack->save();
                    }
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
