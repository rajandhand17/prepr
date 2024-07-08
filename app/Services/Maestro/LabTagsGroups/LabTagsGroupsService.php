<?php

namespace App\Services\Maestro\LabTagsGroups;

use App\Models\LabTagsGroups;

class LabTagsGroupsService
{
    public static function createLabTagsGroups($lab,$newLab)
    {
        try {
            $labTagsGroup=LabTagsGroups::where('lab_id',$lab->id)->get();
            if (count($labTagsGroup) > 0) {
                foreach ($labTagsGroup as $tag) {
                    $labSkillsGroupsStack = new LabTagsGroups();
                    $labSkillsGroupsStack->lab_id = $newLab->id;
                    $labSkillsGroupsStack->foreign_id = $tag->foreign_id;
                    $labSkillsGroupsStack->type = $tag->type;
                    $labSkillsGroupsStack->save();
                }
            }
            return true;
        }catch(\Exception $e){
            return false;
        }
    }
}
