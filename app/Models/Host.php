<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Host extends Model
{
    use HasFactory; 
    use SoftDeletes;

    protected $table = 'hosts';

    protected $fillable = [
        'name',
        'link',
        'image',
        'status',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];


    public function getHosts($language="en",$search=null)
    {    
      try { 
             $host=static::select("id","name","link","image","status");
             if($search!=null){
                $host=$host->where("name","like",'%'.$search.'%');
             }
             $host=$host->take(20)->get();
            //  return $host;
            if(!$host->isEmpty()){
                return $host;
            }

            return false;
        } catch(\Exception $e){
            return false;
        }
    }
}
