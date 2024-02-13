<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Models\CommentSocialActivity;
use Illuminate\Support\Facades\Schema;

class CommentSocialActivitiesService
{
  public function likeOrDislikeComment($component,$request){
      try {
          $checkExistsLikeComment=CommentSocialActivity::where(['comment_id'=>$request->comment_id,"user_id"=>$request->user_id])->first();
          if (!$checkExistsLikeComment){
              $userSetting = new CommentSocialActivity();
          }else{
              $userSetting=$checkExistsLikeComment;
          }
           $likeOrDislike=($component=='like') ? '1' : '2';
           $userSetting->comment_id = $request->comment_id;
           $userSetting->comment_type=$request->comment_type;
           $userSetting->user_id = auth()->user()->id;
           $userSetting->like_dislikes = $likeOrDislike;
           $userSetting->save();
           return $userSetting;
      }catch(\Exception $e){
          dd($e);
          return false;
      }
  }
}
