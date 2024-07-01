<?php

namespace App\Repositories\Api\Discussion;

use App\Helpers\UtilityHelper;
use App\Services\DiscussionService;
use App\Services\DiscussionSocialActivitiesService;
use DB;

class DiscussionRepository implements DiscussionInterface
{
    private $discussionService;

    private $discussionSocialActivitiesService;

    public function __construct(DiscussionService $discussionService, DiscussionSocialActivitiesService $discussionSocialActivitiesService)
    {
        $this->discussionService = $discussionService;
        $this->discussionSocialActivitiesService = $discussionSocialActivitiesService;
    }

    public function index($component, $moduleId, $sortBy)
    {
        try {
            return $this->discussionService->index($component, $moduleId, $sortBy);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function addComment($component, $request, $getComponentId)
    {
        try {
            return $this->discussionService->addComment($component, $request, $getComponentId);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function deleteComment($commentId)
    {
        try {
            $deleteComment = DB::transaction(function () use ($commentId) {
                $discussion = $this->discussionService->deleteDiscussion($commentId);
                $discussionSocialActivities = $this->discussionSocialActivitiesService->deleteDiscussionSocialActivity($discussion);

                return [
                    'discussion'                 => $discussion,
                    'discussionSocialActivities' => $discussionSocialActivities,
                ];
            });
            if ($deleteComment['discussion'] && $deleteComment['discussionSocialActivities']) {
                DB::commit();

                return true;
            }
            DB::rollback();

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function likeDislike($action, $comment_id)
    {
        try {
            return $this->discussionSocialActivitiesService->likeOrDislikeComment($action, $comment_id);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function unLikeOrUnDisLikeComponent($likeOrDislike, $comment_id)
    {
        try {
            return $this->discussionSocialActivitiesService->unLikeOrUnDisLikeComponent($likeOrDislike, $comment_id);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
