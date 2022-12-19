<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Industrie extends Model
{
    use HasFactory;


    public function getAll($industryName=null)
    {   
        try{
            if($industryName==null){
                $industry_list = static::get();
            }else{
                $industry_list = static::where('industries_name','like','%'.$industryName.'%')->get();
            }
            
            if(!$industry_list->isEmpty()){
                return $industry_list;
            }
            return false;
        }
        catch (\Exception $e){
            return false;
        }

    }
}
