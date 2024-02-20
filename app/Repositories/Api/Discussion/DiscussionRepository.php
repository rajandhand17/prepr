<?php

namespace App\Repositories\Api\Discussion;

use App\Services\CommentService;
use App\Services\CommentSocialActivitiesService;
use DB;

class DiscussionRepository implements DiscussionInterface
{
    private $commentService;

    private $commentSocialActivitiesService;

    public function __construct(CommentService $commentService, CommentSocialActivitiesService $commentSocialActivitiesService)
    {
        $this->commentService = $commentService;
        $this->commentSocialActivitiesService = $commentSocialActivitiesService;
    }

    public function index($component, $moduleId)
    {
        try {
            return $this->commentService->index($component,$moduleId);
        }catch(\Exception $e){
            return false;
        }
    }
    public function addComment($component,$request,$getComponentId){
        try {

            return $this->commentService->addComment($component,$request,$getComponentId);
        }catch (\Exception $e){
            return false;
        }
    }
    public function deleteComment($commentId)
    {
        try {
            $deleteComment = DB::transaction(function () use ($commentId) {
                $comment = $this->commentService->deleteComment($commentId);
                $commentSocialActivities = $this->commentSocialActivitiesService->deleteCommentSocialActivity($comment);

                return [
                    'comment'                 => $comment,
                    'commentSocialActivities' => $commentSocialActivities,
                ];
            });
            if ($deleteComment['comment'] && $deleteComment['commentSocialActivities']) {
                DB::commit();

                return true;
            }
            DB::rollback();

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function likeDislike($action,$comment_id){
        try {
            return $this->commentSocialActivitiesService->likeOrDislikeComment($action,$comment_id);
        }catch (\Exception $e){
            return false;
        }
    }

    public function unLikeOrUnDisLikeModule($likeOrDislike, $comment_id)
    {
        try {
            return $this->commentSocialActivitiesService->unLikeOrUnDisLikeModule($likeOrDislike, $comment_id);
        } catch (\Exception $e) {
            return false;
        }
    }
}
