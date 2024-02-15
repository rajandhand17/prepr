<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Models\CommentSocialActivity;
use Illuminate\Support\Facades\Schema;

class CommentSocialActivitiesService
{
  public function likeOrDislikeComment($action,$request){
      try {
          $checkExistsLikeComment=CommentSocialActivity::where(['comment_id'=>$request->comment_id,"user_id"=>auth()->user()->id])->first();
          if (!$checkExistsLikeComment){
              $userSetting = new CommentSocialActivity();
          }else{
              $userSetting=$checkExistsLikeComment;
          }
           $likeOrDislike=($action=='like') ? "1" : "2";
           $userSetting->comment_id = $request->comment_id;
           $userSetting->user_id = auth()->user()->id;
           $userSetting->like_dislikes = $likeOrDislike;
           $userSetting->save();
           return $userSetting;
      }catch(\Exception $e){
          return false;
      }
  }

  public static function checkLikeOrDislikeComment($likeOrDislike,$comment_id){
      try{
          $likeOrDislikeModule=($likeOrDislike=='like') ? '1' : '2';
          $checkExistsLikeComment=CommentSocialActivity::where([
                  "comment_id"=>$comment_id,
                  "user_id"=>auth()->user()->id,
                  "like_dislikes"=>$likeOrDislikeModule,
              ])->first();
          if($checkExistsLikeComment){
              return $checkExistsLikeComment;
          }
          return false;
      }catch(\Exception $e){
          return false;
      }
  }

  public static function unLikeOrUnDisLikeModule($likeOrDislike,$comment_id){
      try{
          $likeOrDislikeModule=($likeOrDislike=='like') ? '1' : '2';
          $checkExistsLikeComment=CommentSocialActivity::where([
              "comment_id"=>$comment_id,
              "user_id"=>auth()->user()->id,
              "like_dislikes"=>$likeOrDislikeModule,
          ])->delete();
          return true;
      }catch (\Exception $e){
          return false;
      }
  }
}
