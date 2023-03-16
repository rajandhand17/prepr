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
use App\Helpers\FileDeleteHelper;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Validation\Rule;

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
        $organization_exists=static::select('id')->where("name",$request->name)->first();
        if($organization_exists==null){
           $profile_images_path=null;
        if($request->profile_image!==null){
            $profile_images_path=FileUploadHelper::uploadImageToS3($request->profile_image);
        }
        $cover_images_path=null;
        if($request->cover_image!==null){
            $cover_images_path=FileUploadHelper::uploadImageToS3($request->cover_image);
        }
        DB::beginTransaction();
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
            $request->organization_id=$organization->id;
            $address=OrganizationAddress::Create($request);
            if($address){
          DB::commit();
          $response= ['success' => true, 'message' => __('responses.create_organization')];
          return $response;
            }
        }else{
            DB::rollback();
           $response= ['success' => false, 'message' => __('responses.create_organization_failed')];
           return $response;
        }
       }else{
        $response= ['success' => false, 'message' => __('responses.organization_name_unique')];
        return $response;
       }

       } catch (\Exception $e) {
        DB::rollback();
        $response= ['success' => false, 'message' => __('responses.send_error')];
        return $response;
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
            $organization->language=($request->has('language')) ?$request->language : $organization->language;
            $organization->name=($request->has('name')) ?$request->name : $organization->name;
            $organization->description=($request->has('description')) ?$request->description : $organization->description;
            $organization->slug=($request->has('name'))?strtolower($request->name):$organization->slug;
            $organization->cover_image=$cover_images_path?$cover_images_path:$organization->cover_image;
            $organization->profile_image=$profile_images_path?$profile_images_path:$organization->profile_image;
            $organization->website=($request->has('website'))?$request->website:$organization->website;
            $organization->about=($request->has('about'))?$request->about:$organization->about;
            $organization->category=($request->has('category'))?$request->category:$organization->category;
           // if($request->status!==null){
            $organization->status=($request->has('status'))?$request->status:$organization->status;
            //}
            $organization->total_employees=($request->has('total_employees'))?$request->total_employees:$organization->total_employees;
            $organization->save();
            if($organization){
                $request->organization_id=$organization->id;
                $organization_address=OrganizationAddress::updates($request);
                $response= ['success' => true, 'message' => __('responses.updated_organization')];
                 return $response;
            }else{
                $response= ['success' => false, 'message' => __('responses.updated_organization_failed')];
                 return $response;
            }
        
       }catch (\Exception $e) {
            $response= ['success' => false, 'message' => __('responses.send_error')];
            return $response;
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
