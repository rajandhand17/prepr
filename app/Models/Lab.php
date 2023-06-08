<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use app\Models\Organization;
use App\Helpers\FileUploadHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Event;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\LabChallenges;
use App\Models\LabResources;
use App\Helpers\LabHelper;
use App\Models\OrganizationInviteUser;
use App\Models\Category;
use AWS\CRT\HTTP\Request;

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
        'total_share',
        'twitter',

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
           return false;
       }
    }


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
    
    public function view($request)
    {  
       try {
        $lab=static::select("title","slug","description","image","member","address","city","country")->where("slug",$request->slug)->first();
       if(!$lab->isEmpty()){
           return $lab;
       }
       return false;
       } catch (\Exception $e) {
        return false;
       }
    }

    public function store($request)
    {  
        try { 
            $lab_count = Lab::where('user_id', Auth::id())->where('is_auto_created', '0')->count();
            dd($lab_count);
            // if (!empty($request->organisation)) {
            //     $organisations_lab_limit = Lab::where('organisation', $request->organisation)->where('is_auto_created', '0')->count();
            //     $labs_limit = PlanSubscriptionHelper::getTotalLimits($request->organisation, 'lab');
            //     if ($labs_limit <= $organisations_lab_limit) {
            //         return redirect()->back()->withErrors($validation)->withInput()->with('limits error', 'You have reached the limit to create lab');
            //     }
            // }
          } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }

    public function checkLabSlug($request)
    {   
        try {
            $slug=Lab::where("slug",$request->slug)->first();
            if(!$slug){
                $response= ['success' => true, 'message' => __('responses.organization_slug_not_exists')];
                return $response;
            }
            return false;
        } catch (\Exception $th) {
           return false;
        }
    }

    public function checkLabName($request)
    {   
        try {
            $slug=Lab::where("title",$request->name)->first();
            if(!$slug){
                $response= ['success' => true, 'message' => __('responses.lab_name_not_exists')];
                return $response;
            }
            return false;
        } catch (\Exception $e) {
           return false;
        }
    }

    public function createform($request)
    {  
        $social_name = SocialLink::pluck('name', 'id')->all();
        $todo_achievement_list = LabHelper::getLabAchievementCondition();
        $social_all = SocialLink::where('icon', '!=', null)->get()->toArray();
        $tags=Tag::where('components','like','lab')->pluck('name', 'id')->all();
        $lab_skills = Skill::pluck('name','id')->all();
        $categories = category::Where('components', 'like', '%lab%')->pluck('name', 'id');
        return ["social_links"=>$social_name,"todo_achievement_list"=>$todo_achievement_list,"social_all"=>$social_all,"tags"=>$tags,"lab_skills"=>$lab_skills,"categories"=>$categories];
      
    }

    public function share($id)
    {
        try {
         $total_share=Lab::select("total_share",'slug')->where("id",$id)->first();
        $new_value=$total_share->total_share+1;
        $lab=Lab::find($id);
        $lab->total_share=(int)$new_value;
        if($lab->save()){
            $response= ['success' => true, 'message' => __('responses.lab_share_message')];
            return $response;
        }
        } catch (\Exception $e) {
            return false;
        }
   
    }
    
    public function getTags($lab_id)
    {
        try {
            $lab_tag=LabTag::get()->where("lab_id",$lab_id);
            if($lab_tag){
                $response= ['success' => true,'data'=>$lab_tag, 'message' => __('responses.lab_tags')];
                return $response;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function labDetail($lab_id)
    {
        try {
            $lab_detailed=Lab::where("id",$lab_id)->first();
            if($lab_detailed){
                $response= ['success' => true,'data'=>$lab_detailed, 'message' => __('responses.lab_detailed_fetech')];
                return $response;
            }
            return false;
        } catch (\Exception $e) {
           return false;
        }
    }
}
