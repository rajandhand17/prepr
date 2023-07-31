<?php

namespace App\Services\Public;

use App\Models\OrganizationSocialActivities;
use Illuminate\Support\Facades\Auth;

class OrganizationSocialActivitiesService
{

    public function checkFollowUnfollowExists($id,$action)
    {
        try {
            return OrganizationSocialActivities::where([
                ['organization_id',"=",$id],
                ['user_id',"=",Auth::user()->id],
                ['follow_unfollow',"=",$action],
            ])->first();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkLikeUnlikeExists($id,$action){
        try {

            return OrganizationSocialActivities::where([
                ['organization_id',"=",$id],
                ['user_id',"=",Auth::user()->id],
                ['like_dislike',"=",$action],
            ])->first();
        } catch (\Exception $e){
           return false;
        }
    }
    public static function follow($organization_id){
        try {

            $checkFollow=OrganizationSocialActivities::where([
                ['user_id',"=",Auth::user()->id],
                ['organization_id',"=",$organization_id],
            ])->first();
            if(!$checkFollow){
                $follow=new OrganizationSocialActivities();
                $follow->user_id=Auth::user()->id;
                $follow->organization_id=$organization_id;
                $follow->follow_unfollow="1";
                if($follow->save()){
                    return true;
                }
            }
            $checkFollow->follow_unfollow='1';
            if($checkFollow->save()){
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
    public static function unfollow($organization_id){
        try {
            $checkFollow=OrganizationSocialActivities::where([
                ['user_id',"=",Auth::user()->id],
                ['organization_id',"=",$organization_id],
            ])->first();
            if(!$checkFollow){
                $follow=new OrganizationSocialActivities();
                $follow->user_id=Auth::user()->id;
                $follow->organization_id=$organization_id;
                $follow->follow_unfollow="2";
                if($follow->save()){
                    return true;
                }
            }
            $checkFollow->follow_unfollow='2';
            if($checkFollow->save()){
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }


    public static function like($organization_id){
        try {
            $checkLike=OrganizationSocialActivities::where([
                ['user_id',"=",Auth::user()->id],
                ['organization_id',"=",$organization_id],
            ])->first();
            if(!$checkLike){
                $follow=new OrganizationSocialActivities();
                $follow->user_id=Auth::user()->id;
                $follow->organization_id=$organization_id;
                $follow->like_dislike="1";
                if($follow->save()){
                    return true;
                }
            }
            $checkLike->like_dislike="1";
            if($checkLike->save()){
                return true;
            }
            return false;
        } catch (\Exception $e){
            return false;
        }
    }

    public static function unlike($organization_id){
        try {
            $checkLike=OrganizationSocialActivities::where([
                ['user_id',"=",Auth::user()->id],
                ['organization_id',"=",$organization_id],
            ])->first();
            if(!$checkLike){
                $follow=new OrganizationSocialActivities();
                $follow->user_id=Auth::user()->id;
                $follow->organization_id=$organization_id;
                $follow->like_dislike="2";
                if($follow->save()){
                    return true;
                }
            }
            $checkLike->like_dislike="2";
            if($checkLike->save()){
                return true;
            }
            return false;
        } catch (\Exception $e){
            return false;
        }
    }

    public  function  share($organization_id){
        try{
            $share = OrganizationSocialActivities::where([
                ['user_id',"=",Auth::user()->id],
                ['organization_id',"=",$organization_id],
            ])->first();
            if(!$share){
                $share=new OrganizationSocialActivities();
                $share->user_id=Auth::user()->id;
                $share->organization_id=$organization_id;
                $share->share="1";
                if($share->save()){
                    return true;
                }
                return false;
            }
            $share->share="1";
            if($share->save()){
                return true;
            }
            return false;
        }catch(\Exception $e){
            dd($e);
            return false;
        }
    }
}
