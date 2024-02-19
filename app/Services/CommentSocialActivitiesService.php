<?php

namespace App\Services;

use App\Models\comment;
use App\Models\CommentSocialActivity;

class CommentSocialActivitiesService
{
  public function likeOrDislikeComment($action,$comment_id){
      try {
          $comment=comment::where('id',$comment_id)->first();
          if(!$comment){
              return false;
          }
          $checkExistsLikeComment=CommentSocialActivity::where(['comment_id'=>$comment_id,"user_id"=>auth()->user()->id])->first();
          if (!$checkExistsLikeComment){
              $userSetting = new CommentSocialActivity();
          }else{
              $userSetting=$checkExistsLikeComment;
          }
           $likeOrDislike=($action=='like') ? "1" : "2";
           $userSetting->comment_id = $comment_id;
           $userSetting->user_id = auth()->user()->id;
           $userSetting->like_dislikes = $likeOrDislike;
           $userSetting->save();
           return $comment;
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
          $comment=comment::where('id',$comment_id)->first();
          if(!$comment){
              return false;
          }
          $likeOrDislikeModule=($likeOrDislike=='like') ? '1' : '2';
          $checkExistsLikeComment=CommentSocialActivity::where([
              "comment_id"=>$comment_id,
              "user_id"=>auth()->user()->id,
              "like_dislikes"=>$likeOrDislikeModule,
          ])->delete();
          return $comment;
      }catch (\Exception $e){
          return false;
      }
  }

    public static function deleteCommentSocialActivity($commentId){
        try {
            $comment = comment::where('id', $request->comment_id)->first();
            if (!$comment) {
                return false;
            }
            $checkExistsLikeComment = CommentSocialActivity::where(['comment_id'=>$request->comment_id, 'user_id'=>auth()->user()->id])->first();
            if (!$checkExistsLikeComment) {
                $userSetting = new CommentSocialActivity();
            } else {
                $userSetting = $checkExistsLikeComment;
            }
            $likeOrDislike = ($action == 'like') ? '1' : '2';
            $userSetting->comment_id = $request->comment_id;
            $userSetting->user_id = auth()->user()->id;
            $userSetting->like_dislikes = $likeOrDislike;
            $userSetting->save();

            return $comment;
        } catch(\Exception $e) {
            return false;
        }
    }




    
}
