<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laratrust\Models\LaratrustTeam;
use Monolog\Processor\WebProcessor;
use Illuminate\Support\Facades\Storage;

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

    public function getOrganization($language='en',$search=null)
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
    
    public function createOrganization($language='en',$user_id,$name, $display_name, $description=null, $profile_image=null, $cover_image=null, $website=null, $about=null, $category=null, $status=null, $total_employees=null)
    {   
       try {
        if($profile_image!==null){
            $profile_image_name = "profile_image_".time().'.'.$profile_image->extension(); 
            $path = Storage::disk('s3')->put('organizations/profile_images', $profile_image_name);
            $path = Storage::disk('s3')->url($path);
            $profile_images_path=$path;
        }else{
            $profile_images_path=null;
        }
        if($cover_image!==null){
            $cover_image_name = "cover_image_".time().'.'.$cover_image->extension();
            $cover_image_path = Storage::disk('s3')->put('organizations/cover_images', $cover_image_name);
            $cover_image_path = Storage::disk('s3')->url($cover_image_path);
            $cover_images_path=$cover_image_path;
        }else{
             $cover_images_path=null;
        }
        $organization=new Organization;
        $organization->language=$language;
        $organization->user_id=$user_id;
        $organization->name=$name;
        $organization->description=$name;
        $organization->slug=strtolower($name);
        $organization->cover_image=$cover_images_path;
        $organization->profile_image=$profile_images_path;
        $organization->website=$website;
        $organization->about=$about;
        $organization->category=$category;
        if($status!==null){
            $organization->status=$status;
        }
        $organization->total_employees=$total_employees;
        if($organization->save()){
            return true;
        }else{
            return false;
        }
       } catch (\Exception $e) {
           return false;
       }
    }
      
    /**update organizations */
    public function updateOrganization($language='en',$organization_id,$user_id,$name, $display_name, $description=null, $profile_image=null, $cover_image=null, $website=null, $about=null, $category=null, $status=null, $total_employees=null)
    {      

       try{  
        if($profile_image!==null){
            $profile_image_name = "profile_image_".time().'.'.$profile_image->extension();
            $path = Storage::disk('s3')->put('organizations/profile_images', $profile_image);
            $path = Storage::disk('s3')->url($path);
            $profile_images_path=public_path('organization_images').$profile_image;
        }else{
            $profile_images_path=null;
        }
        if($cover_image!==null){
            $cover_image_name = "cover_image_".time().'.'.$cover_image->extension();
            $cover_image_path = Storage::disk('s3')->put('organizations/cover_images', $profile_image);
            $cover_image_path = Storage::disk('s3')->url($cover_image_path);
            //$cover_image->move(public_path('organization_images'), $cover_image_name);
            $cover_images_path=$cover_image_path;//public_path('organization_images').$cover_image;
        }else{
             $cover_images_path=null;
        }
        $organization=Organization::find($organization_id);
        $organization->language=$language?$language:$organization->language;
        $organization->user_id=$user_id?$user_id:$organization->user_id;
        $organization->name=$name?$name:$organization->name;
        $organization->description=$description?$description:$organization->description;
        $organization->slug=strtolower($name)?strtolower($name):$organization->slug;
        $organization->cover_image=$cover_images_path?$cover_images_path:$organization->cover_image;
        $organization->profile_image=$profile_images_path?$profile_images_path:$organization->profile_image;
        $organization->website=$website?$website:$organization->website;
        $organization->about=$about?$about:$organization->about;
        $organization->category=$category?$category:$organization->category;
        if($status!==null){
            $organization->status=$status;
        }
        $organization->total_employees=$total_employees?$total_employees:$organization->total_employees;
        if($organization->save()){
            return true;
        }else{
            return false;
        }
       } catch (\Exception $e) {
           return false;
       }
    }



    public function deleteOrganization($language='en',$organization_id=null)
    {
        $organization=Organization::find($organization_id);
        $organization->is_deleted="1";
        if($organization->save()){
             return true;        
        }else{
            return false;
        }
    }

    public function viewOrganization($language='en',$slug)
    {
        try {
            $organization_list=static::select('id','language','name','slug','description','cover_image','profile_image', 'website' ,'about', 'category', 'status', 'is_verified', 'magnet_community_id','total_employees')->where("slug","like",$slug)->take(20)->get();
            if(!$organization_list->isEmpty()){
                return $organization_list;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

}
