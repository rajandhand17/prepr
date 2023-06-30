<?php

namespace App\Services;
use App\Models\LabSkillsGroupsStack;
use DB;
class LabSkillsGroupsStackService{
    public function store($request,$lab){
    try {
        DB::beginTransaction();
        if(isset($request->skills) && !empty($request->skills)){
        $labskills=$request->skills;
        foreach($labskills as $key=>$skills){
            $LabSkillsGroupsStack=new LabSkillsGroupsStack;
            $LabSkillsGroupsStack->lab_id =  $lab->id;
            $LabSkillsGroupsStack->foreign_id= $skills;
            $LabSkillsGroupsStack->type= '0';
            if(!$LabSkillsGroupsStack->save()){
                DB::rollback();
                return false;
            }
        }
        DB::commit();
        return true;
    }
    return false;
    } catch (\Exception $e){
        DB::rollback();
       return false;
    }
    }
}