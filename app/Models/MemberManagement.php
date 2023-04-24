<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Organization;
use App\Models\User;
use DB;

class MemberManagement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'member_management';

    protected $fillable = [
        'type',
        'invite_type',
        'module_id',
        'module_type',
        'inviter_id',
        'invitee_id',
        'role',
        'invite_status',
        'email',
        'email_status',
        'email_response',
        'email_resend_status',
        'email_resend_count',
        'subject_line',
        'email_body',
    ];
    
  /***
     * @return HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'inviter_id');
    }

    
    public function list($component,$slug,$request)
    {    
        try {
            $module_type="0";
            $module_id="";
            if($component=="organisation"){
                $module_type="0";
                $module_id=Organization::select('id')->where("slug",$slug)->first()->id;
            }
            
            if($module_id===null && $module_id==""){
                
                return false;
            }
            $listing=static::with(['user'])->where(["module_id"=>$module_id,"module_type"=>$module_type]);

            if(!empty($request->org_id)){
              
                $listing->where('id', $request->org_id);
            }
            if(!empty($request->role)){
                $listing->where('role', $request->role);
            }
            if (!empty($request->email_status)){    
                if($request->email_status=="scheduled"){
                    $email_status="0";
                }elseif($request->email_status=="sent"){
                    $email_status="1";
                }elseif($request->email_status=="fail"){
                    $email_status="2";
                }
                $listing->where('email_status', $email_status);
            }
            
            if (!empty($request->searchname)){
                $listing->where(function ($q) use ($request) {
                    $q->whereHas('user', function ($q) use ($request){
                        $q->where('username', 'like', '%' . $request->searchname . '%')->orWhere('first_name', 'like', '%' . $request->searchname . '%');
                    })->orWhere('email', 'like', '%' .$request->searchname .'%');
                });
            }
            $listing=$listing->get();
            
            if(!$listing->isEmpty()){
                return $listing;
            }else{
                return false;
            }
       
        }catch (\Exception $e) {
            
            return false;       
        }
    }
    public function deletes($component,$slug,$request)
    {     
        
        try{ 
            $member_manger=static::whereIn("id",$request->id)->delete();
            if($member_manger){
                return true;
            }else{
                return false;
            }
        }catch(\Exception $e){
            return false;        
        }
        
    }

    public function create($invited_members)
    {   
       try{
        if(!empty($invited_members)){
           DB::beginTransaction();
            if(static::insert($invited_members)){
                DB::commit();
               return true;
            }else{
                DB::rollback();
               return false;
            }
        }else{
           return false;
        }
       }catch (\Exception $e) {
        DB::rollback();
        return false;      
       }
    }
}
