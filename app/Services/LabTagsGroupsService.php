<?php

namespace App\Services;
use App\Models\labTagsGroups;
use DB;
class LabTagsGroupsService{
    public function store($request,$lab){
        try {
            DB::beginTransaction();
            if(isset($request->tag) && !empty($request->tag)){
                $labtag=$request->tag;
            foreach($labtag as $key=>$tag){
                $LabTagsGroups=new LabTagsGroups();
                $LabTagsGroups->lab_id =  $lab->id;
                $LabTagsGroups->foreign_id= $tag;
                $LabTagsGroups->type= '0';
                if(!$LabTagsGroups->save()){
                    DB::rollback();
                    return false;
                }
            }
            DB::commit();
            return true;
            }
            return false;
        } catch (\Exception $e){
            return false;
        }
    }
}