<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vertical extends Model
{
    use HasFactory;

    public function getAll($verticalsName=null)
    {    
        
        try{
            if($verticalsName==null){
                $vertical_list = static::get();
            }else{
                $vertical_list = static::where('verticals_name','like','%'.$verticalsName.'%')->get();
            }
            
            if(!$vertical_list->isEmpty()){
                return $vertical_list;
            }
            return false;
        }
        catch (\Exception $e){
            return false;
        }

    }
}
