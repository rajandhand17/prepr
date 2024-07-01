<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\Discussion;
use App\Models\DiscussionSocialActivity;

class DiscussionSocialActivitiesService
{
    public function likeOrDislikeComment($action, $comment_id)
    {
        try {
            $comment = Discussion::where('id', $comment_id)->first();
            if (!$comment) {
                return false;
            }
            $checkExistsLikeComment = DiscussionSocialActivity::where(['comment_id'=>$comment_id, 'user_id'=>auth()->user()->id])->first();
            if (!$checkExistsLikeComment) {
                $userSetting = new DiscussionSocialActivity();
            } else {
                $userSetting = $checkExistsLikeComment;
            }
            $likeOrDislike = ($action == 'like') ? '1' : '2';
            $userSetting->comment_id = $comment_id;
            $userSetting->user_id = auth()->user()->id;
            $userSetting->like_dislikes = $likeOrDislike;
            $userSetting->save();

            return $comment;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkLikeOrDislikeComment($likeOrDislike, $comment_id)
    {
        try {
            $likeOrDislikeModule = ($likeOrDislike == 'like') ? '1' : '2';
            $checkExistsLikeComment = DiscussionSocialActivity::where([
                'comment_id'   => $comment_id,
                'user_id'      => auth()->user()->id,
                'like_dislikes'=> $likeOrDislikeModule,
            ])->first();
            if ($checkExistsLikeComment) {
                return $checkExistsLikeComment;
            }

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function unLikeOrUnDisLikeComponent($likeOrDislike, $comment_id)
    {
        try {
            $comment = Discussion::where('id', $comment_id)->first();
            if (!$comment) {
                return false;
            }
            $likeOrDislikeModule = ($likeOrDislike == 'like') ? '1' : '2';
            $checkExistsLikeComment = DiscussionSocialActivity::where([
                'comment_id'   => $comment_id,
                'user_id'      => auth()->user()->id,
                'like_dislikes'=> $likeOrDislikeModule,
            ])->delete();

            return $comment;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteDiscussionSocialActivity($commentId)
    {
        try {
            $deletedCommentIds = DiscussionSocialActivity::where('id', $commentId)
                ->orWhere('comment_id', $commentId)
                ->pluck('id');
            $deletedComments = DiscussionSocialActivity::whereIn('id', $deletedCommentIds)
                ->delete();

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkLikedOrUnlikedBasedOnUser($commentId, $userId)
    {
        try {
            $comments = DiscussionSocialActivity::where(['comment_id'=>$commentId, 'user_id'=>$userId])->first();
            $response = null;
            if ($comments) {
                $response = $comments->like_dislikes == '1' ? 'like' : 'dislike';
            }

            return $response;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
