<?php

namespace App\Http\Controllers\Api\Discussion;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Discussion\DiscussionRequest;
use App\Repositories\Api\Discussion\DiscussionRepository;
use App\Repositories\Api\Profile\ProfileRepository;

class DiscussionController extends AppBaseController
{
    private $discussionRepository;

    public function __construct(DiscussionRepository $discussionRepository)
    {
        $this->discussionRepository = $discussionRepository;
    }
    public function actionBasedOnAction($component,$action,DiscussionRequest $request){
        try {
            if (!in_array($action, ['add','like','dislikes'])){
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            switch ($action){
                case 'add';
                $discussionRepository=$this->discussionRepository->addComment($component,$request);
                if($discussionRepository){
                    return $this->sendResponse([],__('responses.add_comment_successfully'));
                }
                break;
                case 'like':
                    $like=$this->discussionRepository->likeDislike($component,$request);
                    if($like){
                        return $this->sendResponse([],__('responses.like_successfully'));
                    }
                    break;
                case 'dislikes':
                    $dislike = $this->discussionRepository->likeDislike($component,$request);
                    if($dislike){
                        return $this->sendResponse([],__('responses.like_successfully'));
                    }
                default:
                    return $this->sendError(__('responses.handler_bad_request'), 400);
            }
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteComment(){
        try {
            $delete=$this->discussionRepository->deleteComment($request->id);
            if($delete){
                 return $this->sendResponse([],__('responses.delete_successfully'));
                    }
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
