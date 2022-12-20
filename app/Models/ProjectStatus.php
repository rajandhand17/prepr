<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectStatus extends Model
{
    use HasFactory;

    public function getAll($StatusName=null)
    {   
         
        try{
            if($StatusName==null){
                $type_list = static::get();
            }else{
                $type_list = static::where('status_name','like','%'.$StatusName.'%')->get();
            }
            
            if(!$type_list->isEmpty()){
                return $type_list;
            }
            return false;
        }
        catch (\Exception $e){
            return false;
        }

    }
}
