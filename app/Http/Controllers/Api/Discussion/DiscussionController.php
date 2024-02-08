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
            if (!in_array($action, ['add', 'delete', 'like','dislikes'])) {
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            switch ($action){
                case 'add';
                $discussionRepository=$this->discussionRepository->addComment($component,$request);
                if($discussionRepository){
                    return $this->sendResponse([],__('responses.add_comment_successfully'));
                }
                break;
                case 'delete':
                    $delete=$this->discussionRepository->deleteComment($request);
                    if($delete){
                        return $this->sendResponse([],__('responses.delete_successfully'));
                    }
                    break;
                case 'like':
                    $like=$this->discussionRepository->likeComment($component,$request);
                    if($like){
                        return $this->sendResponse([],__('responses.like_successfully'));
                    }
                    break;
                case 'dislike':
                    $dislike = $this->discussionRepository->likeDislike($component);
            }
        }catch(\Exception $e){
            return false;
        }
    }
}
