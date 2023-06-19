<?php

namespace App\Models;

use App\Helpers\FileDeleteHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laratrust\Models\LaratrustTeam;
use DB;
use App\Models\OrganizationAddress;
use App\Models\OrganizationMember;
use App\Helpers\UtilityHelper;
use App\Helpers\FileUploadHelper;
use App\Http\Requests\Organization\DeleteOrganizationRequest;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Helpers\PlanSubscriptionHelper;
use ChargeBee\ChargeBee\Environment;
use ChargeBee\ChargeBee\Models\Subscription;
use ChargeBee\ChargeBee\Models\Customer;
use ChargeBee\ChargeBee\Models\ItemEntitlement;

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
    public function categoryDetail(): HasOne
    {
        return $this->hasOne(Category::class,'id');
    }

    public function organization(){
        return $this->belongsTo(User::class);
    }

    public function organizationAddress()
    {
        return $this->hasMany(OrganizationAddress::class,'organization_id','id');
    }

    public function organizationMembers()
    {
        return $this->hasMany(OrganizationMember::class,'organization_id','id');
    }

    public function view($search=null,$language='en')
    {       
       try {
        $organization_list=Organization::with('categoryDetail')->with('organizationAddress')->with('organizationMembers');
            if($search!=null){
               $organization_list=$organization_list->where("slug",$search);
             }
             $organization_list=$organization_list->get();
             //check if there are any results
             if(!$organization_list->isEmpty()){
            $organization_list->transform(function ($item) {
                if( $item['status']==0){
                    $item['status'] = 'draft'; 
                }
                if( $item['status']==1){
                    $item['status'] = 'published'; 
                }
                if( $item['status']==2){
                    $item['status'] = 'deactivated'; 
                }
                return $item;
            });
                return $organization_list;
            }
            return "not_exists";
        } catch (\Exception $e) {
        return false;
    }
    }

    public function delete($slug=null,$language='en')
    {   
        try {
            $exists=Organization::select("id")->where("slug",$slug)->first();
            if($exists!==null){
                $organization=Organization::where("slug",$slug)->delete();
                if($organization){
                     return true;        
                }else{
                    return false;
                }
            }else{
                return "not_exists";
            }
          
        } catch (\Exception $e){
            return false;        
        }
    }

    public function list($language='en')
    {
        try {
$organization_list=static::select('id','language','name','slug','description','cover_image','profile_image', 'website' ,'about', 'category', 'status', 'is_verified','total_employees');
             $organization_list=$organization_list->get();
             //check if there are any results
             if(!$organization_list->isEmpty()){
                return $organization_list;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function organizationMemberView($id,$language)
    {
        try {
            $organization_member_list=OrganizationMember::where("organization_id",$id)->get();
            return $organization_member_list;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function organizationMemberdelete($id,$language)
    {
        try {
            $exists=OrganizationMember::select("id")->where("organization_id",$id)->first();
            if($exists!==null){
                $organization=OrganizationMember::where("organization_id",$id)->delete();
                if($organization){
                     return true;        
                }else{
                    return false;
                }
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function organizationMemberupdate($id,$request)
    {
        $profile_images_path=null;
        if($request->profile_image!==null){
            $profile_images_path=FileUploadHelper::uploadImageToS3($request->profile_image,"organization");
            if($profile_images_path==false){
                $response= ['success' => false, 'message' => __('responses.fail_organization_image_upload')];
                return $response;
            }
        }
        
    }

    public function organizationMemberCreate($request)
    {  
        try {
           $organization_members=new OrganizationMember;
           $organization_members->name=$request->organization_id;
           $organization_members->name=$request->name;
           $organization_members->description=$request->description;
           $organization_members->position=$request->position;
           $organization_members->image=$request->image;
           if($organization_members->save()){
            return true;
           }
           return false;
        } catch (\Exception $e) {
            return false;
        }

    }
}
