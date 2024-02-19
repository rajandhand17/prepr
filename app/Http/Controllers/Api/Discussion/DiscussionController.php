<?php

namespace App\Http\Controllers\Api\Discussion;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Discussion\DeleteDiscussionRequest;
use App\Http\Requests\Discussion\DiscussionRequest;
use App\Http\Resources\Discussion\DiscussionResource;
use App\Models\Lab;
use App\Models\MemberManagement;
use App\Repositories\Api\Discussion\DiscussionRepository;
use App\Services\CommentService;
use App\Services\CommentSocialActivitiesService;
use App\Services\Manage\LabService;
use Illuminate\Http\Request;

class DiscussionController extends AppBaseController
{
    private $discussionRepository;

    public function __construct(DiscussionRepository $discussionRepository)
    {
        $this->discussionRepository = $discussionRepository;
    }

    public function index($component,$moduleId){
        try{
            if (!in_array($component, ['member','lab','project','challenge'])){
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            $list=$this->discussionRepository->index($component,$moduleId);
            if($list->count()>0){
                return $this->sendResponse(DiscussionResource::collection($list),__('responses.comments_lists_successfully'));
            }
            return $this->sendError(__('responses.comments_lists_failed'), 404);
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
    public function componentBasedOnAction($component,$slug,DiscussionRequest $request,$activity = null){
        try {
            if (!in_array($component, ['member','lab','project','challenge'])){
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            if (!in_array($activity, [null,'add','like','dislikes'])){
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            switch ($component){
                case 'member':
                    $getComponentId=[];
                   break;
                case 'lab':
                   $getComponentId=LabService::getLabBasedOnSlug($slug)->id;
                   break;
                case 'project':
                    $getComponentId=[];
                   break;
                case 'challenge':
                    $getComponentId=[];
                   break;
                default:
                 return $this->sendError(__('responses.handler_bad_request'), 400);
                 break;
            }
            switch ($activity) {
                case 'add':
                $addComment=$this->discussionRepository->addComment($component,$request,$getComponentId);
                if($addComment){
                    return $this->sendResponse(DiscussionResource::make($addComment),__('responses.add_comment_successfully'));
                }
                break;
                case 'like':
                    $checkLikedOrNot=CommentSocialActivitiesService::checkLikeOrDislikeComment($activity,$getComponentId);
                    if($checkLikedOrNot){
                        $like=$this->discussionRepository->unLikeOrUnDisLikeModule($activity,$getComponentId);
                    }else{
                        $like=$this->discussionRepository->likeDislike($activity,$getComponentId);
                    }
                    if($like){
                        return $this->sendResponse(DiscussionResource::make($like),__('responses.like_successfully'));
                    }
                    break;
                case 'dislikes':
                    $checkDisLikedOrNot=CommentSocialActivitiesService::checkLikeOrDislikeComment($action,$request->comment_id);
                    if($checkDisLikedOrNot){
                        $dislike=$this->discussionRepository->unLikeOrUnDisLikeModule($action,$request->comment_id);
                    }else{
                        $dislike = $this->discussionRepository->likeDislike($component,$request);
                    }
                    if($dislike){
                        return $this->sendResponse(DiscussionResource::make($dislike),__('responses.like_successfully'));
                    }

                default:
                    return $this->sendError(__('responses.handler_bad_request'), 400);
                    break;
            }
            return $this->sendError(__('responses.handler_bad_request'), 400);
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteComment($commentId){
        try {
            $checkCommentId=CommentService::checkCommentIdExistsOrNot($commentId);
            if (!$checkCommentId){
                return $this->sendError(__('responses.not_exists_id'),422);
            }
            $delete=$this->discussionRepository->deleteComment($commentId);
            if($delete){
                 return $this->sendResponse([],__('responses.delete_successfully'));
             }
            return $this->sendError(__('responses.send_error'), 400);
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
