<?php

namespace App\Http\Controllers\Api\Discussion;

use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Discussion\DiscussionRepository;
use App\Repositories\Api\Profile\ProfileRepository;

class DiscussionController extends AppBaseController
{
    private $discussionRepository;

    public function __construct(DiscussionRepository $discussionRepository)
    {
        $this->discussionRepository = $discussionRepository;
    }

    public function actionBasedOnAction($action,$request){
        try {
            switch ($action){
                case 'comment';
                $discussionRepository=$this->discussionRepository->addComment($request);
                if($discussionRepository){
                    return $this->sendResponse([],__('responses.add_comment_successfully'));
                }
                break;
                case 'delete':
                    $delete=$this->discussionRepository->deleteComment($request);
            }
        }catch(\Exception $e){
            return false;
        }
    }
}
