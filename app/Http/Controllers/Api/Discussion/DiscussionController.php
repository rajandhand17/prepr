<?php

namespace App\Http\Controllers\Api\Discussion;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Discussion\AddCommentRequest;
use App\Http\Requests\Discussion\DiscussionRequest;
use App\Http\Resources\Discussion\DiscussionResource;
use App\Repositories\Api\Discussion\DiscussionRepository;
use App\Services\DiscussionService;
use App\Services\DiscussionSocialActivitiesService;
use App\Services\Manage\LabService;

class DiscussionController extends AppBaseController
{
    private $discussionRepository;

    public function __construct(DiscussionRepository $discussionRepository)
    {
        $this->discussionRepository = $discussionRepository;
    }

    public function index($component, $slug)
    {
        try {
            if (!in_array($component, ['member', 'lab', 'project', 'challenge'])) {
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
            $list = $this->discussionRepository->index($component, $getComponentId);
            if ($list->count() > 0) {
                return $this->sendResponse(DiscussionResource::collection($list), __('responses.comments_lists_successfully'));
            }
            return $this->sendResponse([], __('responses.comments_lists_successfully'));
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addComment($component,$slug,AddCommentRequest $request){
        try {
            if (!in_array($component, ['member', 'lab', 'project', 'challenge'])) {
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
            $addComment=$this->discussionRepository->addComment($component,$request,$getComponentId);
            if($addComment){
                return $this->sendResponse(DiscussionResource::make($addComment),__('responses.add_comment_successfully'));
            }
            return $this->sendError(__('responses.add_comment_failed'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
    public function socialActivity($component,$slug,$id,$activity = null){
        try {
            if (!in_array($component, ['member', 'lab', 'project', 'challenge'])) {
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            if (!in_array($activity, ['like','un-like'])){
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            $checkCommentIdExistsOrNot=DiscussionService::checkCommentIdExistsOrNot($id);
            if(!$checkCommentIdExistsOrNot){
                return $this->sendError(__('responses.not_exists_id'),422);
            }
            switch ($activity) {
                case 'like':
                    $checkLikedOrNot=DiscussionSocialActivitiesService::checkLikeOrDislikeComment($activity,$id);
                    if($checkLikedOrNot){
                        $like=$this->discussionRepository->unLikeOrUnDisLikeModule($activity,$id);
                    }else{
                        $like=$this->discussionRepository->likeDislike($activity,$id);
                    }
                    if ($like) {
                        return $this->sendResponse(DiscussionResource::make($like), __('responses.like_successfully'));
                    }
                    break;
                case 'un-like':
                    $checkDisLikedOrNot = DiscussionSocialActivitiesService::checkLikeOrDislikeComment($activity, $id);
                    if ($checkDisLikedOrNot) {
                        $dislike = $this->discussionRepository->unLikeOrUnDisLikeModule($activity,$id);
                    } else {
                        $dislike = $this->discussionRepository->likeDislike($component,$id);
                    }
                    if ($dislike) {
                        return $this->sendResponse(DiscussionResource::make($dislike), __('responses.like_successfully'));
                    }
                default:
                    return $this->sendError(__('responses.handler_bad_request'), 400);
                    break;
            }
            return $this->sendError(__('responses.handler_bad_request'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteComment($component,$slug)
    {
        try {
            switch ($component){
                case 'member':
                    $discussionId=[];
                    break;
                case 'lab':
                    $discussionId=LabService::getLabBasedOnSlug($slug)->id;
                    break;
                case 'project':
                    $discussionId=[];
                    break;
                case 'challenge':
                    $discussionId=[];
                    break;
                default:
                    return $this->sendError(__('responses.handler_bad_request'), 400);
                    break;
            }
            $checkCommentId = DiscussionService::checkCommentIdExistsOrNot($discussionId);
            if (!$checkCommentId) {
                return $this->sendError(__('responses.not_exists_id'), 422);
            }
            $delete = $this->discussionRepository->deleteComment($discussionId);
            if ($delete){
                return $this->sendResponse([], __('responses.delete_successfully'));
            }
            return $this->sendError(__('responses.send_error'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
