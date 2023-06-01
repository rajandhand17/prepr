<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lab extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table="labs";

    protected $fillable = [
        'language',
        'slug',
        'user_id',
        'organisation',
        'title',
        'verification',
        'description',
        'category',
        'privacy',
        'mediaType',
        'image',
        'member',
        'member_type',
        'latitute',
        'longitude',
        'address',
        'city',
        'country',
        'challnges',
        'lab_skills',
        'tag',
        'status',
        'phone',
        'company',
        'email',
        'website',
        'facebook',
        'linked',
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    
    public function list($request)
    {
        try { 
            $lab=static::select("title","slug","description","image","member","address","city","country");
            
            $lab=$lab->take(20)->get();
           if(!$lab->isEmpty()){
               return $lab;
           }
           return false;
       } catch(\Exception $e){
        return $e;
           return false;
       }
    }
       /**delete lab based on slug */
    public function deletes($request)
    {
        try{ 
            $is_exists=static::where("slug",$request->slug)->first();
            if(!$is_exists){
                $response= ['success' => false, 'message' => __('notification.notification_lab_nf')];
               return $response;
            }
            $labs=static::where("slug",$request->slug)->delete();
            if($labs){
                $response= ['success' => true, 'message' => __('notification.notification_lds')];
               return $response;
            }else{
                return false;
            }
        }catch(\Exception $e){
            return false;        
        }
    }
    /**Search particular lab based on slug */
    public function view($request)
    {  
       try {
        $lab=static::select("title","slug","description","image","member","address","city","country")->where("slug",$request->slug)->first();
        return $lab;
       if(!$lab->isEmpty()){
           return $lab;
       }
       return false;
       } catch (\Exception $e) {
        return false;
       }
    }

    /**Store lab in database */
    public function store($request)
    {
        try{
            
        }catch(\Exception $e){
           return false; 
        }
    }
}
