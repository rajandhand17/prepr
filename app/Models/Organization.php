<?php

namespace App\Models;

use App\Helpers\FileDeleteHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laratrust\Models\LaratrustTeam;
use DB;
use App\Models\OrganizationAddress;
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

    public function Organization(){
        return $this->belongsTo(User::class);
    }

    public function view($search=null,$language='en')
    {       
       try {
        $organization_list=Organization::select('id','language','name','slug','description','cover_image','profile_image', 'website' ,'about', 'category', 'status', 'is_verified', 'magnet_community_id','total_employees')->with('categoryDetail');
            if($search!=null){
               $organization_list=$organization_list->where("slug",$search);
             }
             $organization_list=$organization_list->get();
             //check if there are any results
             if(!$organization_list->isEmpty()){
                 return $organization_list;
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
  
    public function create($request)
    {    
        try{
        $organization_exists=static::select('id')->where("name",$request->name)->withTrashed()->first();
        if($organization_exists==null){
           $profile_images_path=null;
        if($request->profile_image!==null){
            $profile_images_path=FileUploadHelper::uploadImageToS3($request->profile_image,"organization");
            if($profile_images_path==false){
                $response= ['success' => false, 'message' => __('responses.fail_organization_image_upload')];
                return $response;   
            }
        }
        $cover_images_path=null;
        if($request->cover_image!==null){
            $cover_images_path=FileUploadHelper::uploadImageToS3($request->cover_image,"organization");
            if($cover_images_path==false){
                $response= ['success' => false, 'message' => __('responses.fail_organization_image_upload')];
                return $response;
            }
        }
        DB::beginTransaction();
        $model=new Organization;
        $organization=new Organization;
        $organization->language=($request->has('language'))?$request->language:null;
        $organization->user_id=$request->user_id;
        $organization->name=$request->name;
        $organization->description=($request->has('description'))?$request->description:null;
        $organization->slug=UtilityHelper::generateSlug($request->name,$model);
        $organization->cover_image=$cover_images_path;
        $organization->profile_image=$profile_images_path;
        $organization->website=($request->has('website'))?$request->website:null;
        $organization->about=($request->has('about'))?$request->about:null;
        $organization->category=$request->category;
        if($request->status!==null){
            $organization->status=$request->status;
        }
        $organization->total_employees=$request->total_employees;
        if($organization->save()){
            $cust_id = PlanSubscriptionHelper::getCustomer(auth()->user()->email);
            if($cust_id != []){
                $planSubscribed  = PlanSubscriptionHelper::subscribePlan($cust_id, 'free-plan-CAD-Yearly', $organization->id);
            }
            if($request->has('address') && $request->has('city') && $request->has('state') && $request->has('country') && $request->has('zip_code')){
                $request->organization_id=$organization->id;
                $address=OrganizationAddress::create($request);
                if($address){
                    DB::commit();
                    $data=["name"=>$organization->name,"slug"=> $organization->slug];
                    $response= ['success' => true,"data"=>$data, 'message' => __('responses.create_organization')];
                    return $response;
               }else{
                    DB::rollback();
                    $response= ['success' => false, 'message' => __('responses.create_organization_failed')];
                    return $response;
               }
            }
            DB::commit();
            $response= ['success' => true, 'message' => __('responses.create_organization')];
            return $response;
        }else{
            DB::rollback();
           $response= ['success' => false, 'message' => __('responses.create_organization_failed')];
           return $response;
        }
       }else{
        $organization_trashed_exists=static::select('id')->where("name",$request->name)->onlyTrashed()->first();
        if($organization_trashed_exists!=null){
            $response= ['success' => false, 'message' => __('responses.trashed_records')];
            return $response;
        }
        $response= ['success' => false, 'message' => __('responses.organization_name_unique')];
        return $response;
       }

       } catch (\Exception $e) {
        DB::rollback();
        return $e;
        $response= ['success' => false, 'message' => __('responses.send_error')];
        return $response;
       }
    }
      
    /**update organizations */
    public function updates($slug,$request)
    {   
       try{
        $profile_images_path=null;
        if($request->profile_image!==null){
            $profile_images_path=FileUploadHelper::uploadImageToS3($request->profile_image,"organization");
            if($profile_images_path==false){
                $response= ['success' => false, 'message' => __('responses.fail_organization_image_upload')];
                return $response;
            }
        }
        $cover_images_path=null;
        if($request->cover_image!==null){
            $cover_images_path=FileUploadHelper::uploadImageToS3($request->cover_image,"organization");
        }  
            $organization=static::select('id','language','name','slug','description','cover_image','profile_image', 'website' ,'about', 'category', 'status', 'is_verified','total_employees')->where("slug",$slug)->first();
            if($organization!==null){
            $organization->language=($request->has('language')) ?$request->language : $organization->language;
            $organization->name=($request->has('name')) ?$request->name : $organization->name;
            $organization->description=($request->has('description')) ?$request->description : $organization->description;
            $organization->slug=($request->has('name'))?strtolower($request->name):$organization->slug;
            $organization->cover_image=$cover_images_path?$cover_images_path:$organization->cover_image;
            $organization->profile_image=$profile_images_path?$profile_images_path:$organization->profile_image;
            $organization->website=($request->has('website'))?$request->website:$organization->website;
            $organization->about=($request->has('about'))?$request->about:$organization->about;
            $organization->category=($request->has('category'))?$request->category:$organization->category;
            $organization->status=($request->has('status'))?$request->status:$organization->status;
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
        }

        $response= ['success' => false, 'message' => __('responses.organization_not_exists')];
                 return $response;
       }catch (\Exception $e) {
            $response= ['success' => false, 'message' => __('responses.send_error')];
            return $response;
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
 
}
