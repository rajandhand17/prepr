<?php

namespace App\Http\Controllers\Api\Discussion;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Discussion\DiscussionRequest;
use App\Http\Resources\Discussion\DiscussionResource;
use App\Models\CommentSocialActivity;
use App\Repositories\Api\Discussion\DiscussionRepository;
use App\Services\CommentSocialActivitiesService;
use Illuminate\Http\Request;

class DiscussionController extends AppBaseController
{
    private $discussionRepository;

    public function __construct(DiscussionRepository $discussionRepository)
    {
        $this->discussionRepository = $discussionRepository;
    }

    public function index($component,Request $request){
        try{
            $list=$this->discussionRepository->index($component,$request->module_id);
            if($list){
                return $this->sendResponse(DiscussionResource::collection($list),__('responses.add_comment_successfully'));
            }
            return false;
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
    public function actionBasedOnAction($component,$action,DiscussionRequest $request){
        try {
            if (!in_array($action, ['add','like','dislikes'])){
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            switch ($action){
                case 'add':
                $addComment=$this->discussionRepository->addComment($component,$request);
                if($addComment){
                    return $this->sendResponse(DiscussionResource::make($addComment),__('responses.add_comment_successfully'));
                }
                break;
                case 'like':
                    $checkLikedOrNot=CommentSocialActivitiesService::checkLikeOrDislikeComment($action,$request->comment_id);
                    if($checkLikedOrNot){
                        $unLike=$this->discussionRepository->unLikeOrUnDisLikeModule($action,$request->comment_id);
                        if($unLike){
                            return $this->sendResponse([],__("responses.unlike_successfully"));
                        }
                    }else{
                        $like=$this->discussionRepository->likeDislike($action,$request);
                        if($like){
                            return $this->sendResponse(DiscussionResource::make($like),__('responses.like_successfully'));
                        }
                    }
                    break;
                case 'dislikes':
                    $checkDisLikedOrNot=CommentSocialActivitiesService::checkLikeOrDislikeComment($action,$request->comment_id);
                    if($checkDisLikedOrNot){
                        $unDisLike=$this->discussionRepository->unLikeOrUnDisLikeModule($action,$request->comment_id);
                        if($unDisLike){
                            return $this->sendResponse([],__("responses.un_dislike_successfully"));
                        }
                    }else{
                        $dislike = $this->discussionRepository->likeDislike($component,$request);
                        if($dislike){
                            return $this->sendResponse([],__('responses.dislike_successfully'));
                        }
                    }

                default:
                    return $this->sendError(__('responses.handler_bad_request'), 400);

                    break;
            }
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteComment($commentId){
        try {
            $delete=$this->discussionRepository->deleteComment($commentId);
            if($delete){
                 return $this->sendResponse([],__('responses.delete_successfully'));
                    }
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
