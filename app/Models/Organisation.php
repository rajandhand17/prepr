<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organisation extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'organisations';

    protected $fillable = [
        'user_id',
        'name',
        'slug',
      //  'vanity_slug',
        'description',
        'cover_image',
        'profile_image',
        'about',
         'category',
        //  'latitude',
        //  'longitude',
        //  'address',
         'vanity_link',
         'status',
         'labs_limit',
       //  'challenges_limit',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function checkOrgnization($request)
    {  
        try {
            $checkorganization=Organisation::select("id")->where("name",$request->organization_name)->first();
            if($checkorganization){
                return response()->json(['status'=>"fail","message"=>__("responses.organization_exists")]);
            }
             return response()->json(["status"=>"success","message"=>__("responses.organization_not_exists")]);
        } catch (\Exception $e){
            return response()->json(["status"=>"fail","message"=>$e->getMessage()],200);
        }
    }

}
