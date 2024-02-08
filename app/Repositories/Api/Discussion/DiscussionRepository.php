<?php

namespace App\Repositories\Api\Discussion;

use App\Services\CommentService;
use App\Services\CommentSocialActivitiesService;
use App\Services\FriendService;
use App\Services\UserAddressService;
use App\Services\UserCertificateService;
use App\Services\UserEducationService;
use App\Services\UserExperienceService;
use App\Services\UserPatentService;
use App\Services\UserPersonalService;
use App\Services\UserService;
use App\Services\UserSkillsService;
use App\Services\UserTagsService;
use DB;

class DiscussionRepository implements DiscussionInterface
{
    private $commentService;

    private $commentSocialActivitiesService;
    public function __construct(CommentService $commentService,CommentSocialActivitiesService $commentSocialActivitiesService){
        $this->commentService=$commentService;
        $this->commentSocialActivitiesService=$commentSocialActivitiesService;
    }

    public function addComment($component,$request){
        try {

            return $this->commentService->addComment($component,$request);
        }catch (\Exception $e){
            return false;
        }
    }

    public function deleteComment($request){
        try {
            return $this->commentService->deleteComment($request);
        }catch (\Exception $e){
            return false;
        }
    }

    public function likeDislike($component,$request){
        try {
            return $this->commentSocialActivitiesService->likeOrDislikeComment($component,$request);
        }catch (\Exception $e){
            return false;
        }
    }

}
