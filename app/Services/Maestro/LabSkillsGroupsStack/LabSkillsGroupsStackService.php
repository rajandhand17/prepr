<?php

namespace App\Services\Maestro\LabSkillsGroupsStack;

use App\Models\LabSkillsGroupsStack;

class LabSkillsGroupsStackService
{
    public static function createLabSkillsGroupsStack($lab,$newLabId)
    {
        try {
            $getSkillsBasedOnLab=LabSkillsGroupsStack::where('lab_id',$lab->id)->get();
            if (count($getSkillsBasedOnLab) > 0) {
                foreach ($getSkillsBasedOnLab as $skill) {
                    $labSkillsGroupsStack = new LabSkillsGroupsStack();
                    $labSkillsGroupsStack->lab_id = $newLabId->id;
                    $labSkillsGroupsStack->foreign_id = $skill->foreign_id;
                    $labSkillsGroupsStack->type =$skill->type;
                    $labSkillsGroupsStack->save();
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
