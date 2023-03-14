<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laratrust\Models\LaratrustTeam;
use Monolog\Processor\WebProcessor;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\OrganizationAddress;
use App\Helpers\FileUploadHelper;
use Intervention\Image\ImageManagerStatic as Image;

class Organization extends LaratrustTeam
{   
    use SoftDeletes;
    use HasFactory;

    protected $table = 'organizations';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'language',
        'user_id',
        'slug',
        'cover_image',
        'profile_image',
        'website',
        'about',
        'category',
        'status',
        'is_verified',
        'magnet_community_id',
        'total_employees',
    ];

    public function list($language='en',$search=null)
    {
       try {
            $organization_list=static::select('id','language','name','slug','description','cover_image','profile_image', 'website' ,'about', 'category', 'status', 'is_verified', 'magnet_community_id','total_employees');
            if($search!=null){
                $organization_list=$organization_list->where("name","like",'%'.$search.'%');
             }
             $organization_list=$organization_list->take(20)->get();
             //check if there are any results
             if(!$organization_list->isEmpty()){
                return $organization_list;
            }
            return false;
       } catch (\Exception $e) {
          return false;
       }
    }
  
    public function create($request)
    {    
       try {
        $profile_images_path=null;
        if($request->profile_image!==null){
            $profile_images_path=FileUploadHelper::uploadImageToS3($request->profile_image);
        }
        $cover_images_path=null;
        if($request->cover_image!==null){
            $cover_images_path=FileUploadHelper::uploadImageToS3($request->cover_image);
        }
        //DB::beginTransaction();
        $organization=new Organization;
        $organization->language=$request->language;
        $organization->user_id=$request->user_id;
        $organization->name=$request->name;
        $organization->description=$request->description;
        $organization->slug=strtolower($request->name);
        $organization->cover_image=$cover_images_path;
        $organization->profile_image=$profile_images_path;
        $organization->website=$request->website;
        $organization->about=$request->about;
        $organization->category=$request->category;
        if($request->status!==null){
            $organization->status=$request->status;
        }
        $organization->total_employees=$request->total_employees;
        if($organization->save()){
            $address=OrganizationAddress::Create($request);
            if($address){
          //      DB::commit();
              return true;
            }
        }else{
           // DB::rollback();
            return false;
        }
       } catch (\Exception $e) {
        //DB::rollback();
        return false;
       }
    }
      
    /**update organizations */
    public function updates($request)
    {    
       try{
        $profile_images_path=null;
        if($request->profile_image!==null){
            $profile_images_path=FileUploadHelper::uploadImageToS3($request->profile_image);
        }
        $cover_images_path=null;
        if($request->cover_image!==null){
            $cover_images_path=FileUploadHelper::uploadImageToS3($request->cover_image);
        }
            $organization=static::select('id','language','name','slug','description','cover_image','profile_image', 'website' ,'about', 'category', 'status', 'is_verified','total_employees')->where("slug",$request->slug)->first();
            $organization->language=$request->language?$request->language:$organization->language;
            $organization->name=$request->name?$request->name:$organization->name;
            $organization->description=$request->description?$request->description:$organization->description;
            $organization->slug=strtolower($request->name)?strtolower($request->name):$organization->slug;
            $organization->cover_image=$cover_images_path?$cover_images_path:$organization->cover_image;
            $organization->profile_image=$profile_images_path?$profile_images_path:$organization->profile_image;
            $organization->website=$request->website?$request->website:$organization->website;
            $organization->about=$request->about?$request->about:$organization->about;
            $organization->category=$request->category?$request->category:$organization->category;
            if($request->status!==null){
                $organization->status=$request->status;
            }
            $organization->total_employees=$request->total_employees?$request->total_employees:$organization->total_employees;
            $organization->save();
            if($organization){
                $organization_address=OrganizationAddress::updates($request);
                return true;
            }else{
                return false;
            }
        
       } catch (\Exception $e) {
           return false;
       }
    }

    public function delete($language='en',$slug=null)
    {   
        try {
            $organization=Organization::where("slug","like",$slug)->delete();
            if($organization){
                 return true;        
            }else{
                return false;
            }
        } catch (\Exception $e){
            return false;        
        }
        
    }

    public function view($language='en',$slug)
    {
        try {
$organization_list=static::select('id','language','name','slug','description','cover_image','profile_image', 'website' ,'about', 'category', 'status', 'is_verified','total_employees');
            if($slug!=null){
                $organization_list=$organization_list->where("name","like",'%'.$slug.'%');
             }
             $organization_list=$organization_list->take(20)->get();
             //check if there are any results
             if(!$organization_list->isEmpty()){
                return $organization_list;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

}
