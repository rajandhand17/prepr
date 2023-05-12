<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lab extends Model
{
    use HasFactory;

    public function list($request)
    {
        $data=static::get();
        return $data;
    }

    public function create($request)
    {
        
    }

    public function deletes($request)
    {
        try{ 
            $labs=static::whereIn("id",$request->id)->delete();
            if($labs){
                return true;
            }else{
                return false;
            }
        }catch(\Exception $e){
            return false;        
        }
    }
    
}
