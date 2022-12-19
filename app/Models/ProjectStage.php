<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectStage extends Model
{
    use HasFactory;

        
    public function getAll($typeName=null)
    {   
        try{
            if($typeName==null){
                $stage_list = static::get();
            }else{
                $stage_list = static::where('stage_name','like','%'.$typeName.'%')->get();
            }
            if(!$stage_list->isEmpty()){
                return $stage_list;
            }
            return false;
        }
        catch (\Exception $e){
            return false;
        }

    }
}
