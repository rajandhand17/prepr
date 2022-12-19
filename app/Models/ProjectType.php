<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    use HasFactory;
    
    public function getAll($typeName=null)
    {   
        try{
            if($typeName==null){
                $type_list = static::get();
            }else{
                $type_list = static::where('type_name','like','%'.$typeName.'%')->get();
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
