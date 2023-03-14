<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laratrust\Models\LaratrustTeam;
use Monolog\Processor\WebProcessor;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\OrganizationAddress;
use Aws\S3\S3Client;
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
    
    function hs_png2webp($source_file, $destination_file, $compression_quality = 100)
    {   
        if($source_file->extension()=="jpg" || $source_file->extension()=="jpeg"){
            $image = imagecreatefromjpeg($source_file);
            $result = imagewebp($image, $destination_file, $compression_quality);
            if (false === $result) {
                return false;
            }
            imagedestroy($image);
            return $destination_file;
        }
        
    }

    public function create($language='en',$user_id,$name, $display_name, $description=null, $profile_image=null, $cover_image=null, $website=null, $about=null, $category=null, $status=null, $total_employees=null, $latitude=null, $longitude=null, $address=null, $city=null, $state=null, $country=null, $zipcode=null)
    {    
       try {
        if($profile_image!==null){
            $image = Image::make($profile_image->getRealPath());
            $image->encode('webp', 75);
            $image_contents = $image->__toString();
            $webp_path = 'organizations/profile_images/'.time().'.webp';
            $path=Storage::disk('s3')->put($webp_path, $image_contents);
            $path = Storage::disk('s3')->url($webp_path);
            $profile_images_path=$path;
        }else{
            $profile_images_path=null;
        }
        if($cover_image!==null){
            $image_cover = Image::make($cover_image->getRealPath());
            $image_cover->encode('webp', 75);
            $image_contents_cover = $image_cover->__toString();
            $webp_path_cover = 'organizations/cover_images/'.time().'.webp';
            $path_cover=Storage::disk('s3')->put($webp_path_cover, $image_contents_cover);
            $path_cover = Storage::disk('s3')->url($webp_path_cover);
            $cover_images_path=$path_cover;
        }else{
             $cover_images_path=null;
        }
        DB::beginTransaction();
        $organization=new Organization;
        $organization->language=$language;
        $organization->user_id=$user_id;
        $organization->name=$name;
        $organization->description=$description;
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
            $address=OrganizationAddress::Create($organization->id,$latitude,$longitude,$address,$city,$state,$country,$zipcode);
            if($address){
                DB::commit();
              return true;
            }
        }else{
            DB::rollback();
            return false;
        }
       } catch (\Exception $e) {
        DB::rollback();
        return false;
       }
    }
      
    /**update organizations */
    public function updates($language='en',$slug,$name=null, $description=null, $profile_image=null, $cover_image=null, $website=null, $about=null, $category=null, $status=null, $total_employees=null,$organization_id=null, $latitude=null, $longitude=null, $address=null, $city=null, $state=null, $country=null, $zipcode=null)
    {    
       try{
            if($profile_image!==null){
               
        $image = Image::make($profile_image->getRealPath());
        $image->encode('webp', 75);
        $image_contents = $image->__toString();
        $webp_path = 'organizations/profile_images/'.time().'.webp';
        $path=Storage::disk('s3')->put($webp_path, $image_contents);
        $path = Storage::disk('s3')->url($webp_path);
        $profile_images_path=$path;
            }else{
                $profile_images_path=null;
            }
            if($cover_image!==null){
                $image_cover = Image::make($cover_image->getRealPath());
            $image_cover->encode('webp', 75);
            $image_contents_cover = $image_cover->__toString();
            $webp_path_cover = 'organizations/cover_images/'.time().'.webp';
            $path_cover=Storage::disk('s3')->put($webp_path_cover, $image_contents_cover);
            $path_cover = Storage::disk('s3')->url($webp_path_cover);
            $cover_images_path=$path_cover;
            }else{
                 $cover_images_path=null;
            }
            $organization=static::select('id','language','name','slug','description','cover_image','profile_image', 'website' ,'about', 'category', 'status', 'is_verified', 'magnet_community_id','total_employees')->where("slug","like",$slug)->take(20)->first();
            $organization->language=$language?$language:$organization->language;
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
            $organization->save();
            if($organization){
                $organization_address=OrganizationAddress::updates($organization_id, $latitude, $longitude, $address, $city, $state, $country, $zipcode);
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
            $organization_list=static::select('id','language','name','slug','description','cover_image','profile_image', 'website' ,'about', 'category', 'status', 'is_verified', 'magnet_community_id','total_employees');
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
