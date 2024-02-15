<?php

namespace App\Repositories\Api\Discussion;

use App\Services\CommentService;
use App\Services\CommentSocialActivitiesService;
use DB;

class DiscussionRepository implements DiscussionInterface
{
    private $commentService;

    private $commentSocialActivitiesService;
    public function __construct(CommentService $commentService,CommentSocialActivitiesService $commentSocialActivitiesService){
        $this->commentService=$commentService;
        $this->commentSocialActivitiesService=$commentSocialActivitiesService;
    }

    public function index($component,$commentId){
        try {
            return $this->commentService->index($component,$commentId);
        }catch(\Exception $e){
            return false;
        }
    }
    public function addComment($component,$request){
        try {

            return $this->commentService->addComment($component,$request);
        }catch (\Exception $e){
            return false;
        }
    }

    public function deleteComment($commentId){
        try {
            return $this->commentService->deleteComment($commentId);
        }catch (\Exception $e){
            return false;
        }
    }

    public function likeDislike($action,$request){
        try {
            return $this->commentSocialActivitiesService->likeOrDislikeComment($action,$request);
        }catch (\Exception $e){
            return false;
        }
    }

    public function unLikeOrUnDisLikeModule($likeOrDislike,$comment_id){
        try {
            return $this->commentSocialActivitiesService->unLikeOrUnDisLikeModule($likeOrDislike,$comment_id);
        }catch (\Exception $e){
            return false;
        }
    }

}
