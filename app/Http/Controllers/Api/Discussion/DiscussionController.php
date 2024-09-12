<?php

namespace App\Http\Controllers\Api\Discussion;

use App\Helpers\LearningPointsHelper;
use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Discussion\AddCommentRequest;
use App\Http\Resources\Discussion\DiscussionResource;
use App\Repositories\Api\Discussion\DiscussionRepository;
use App\Services\DiscussionService;
use App\Services\DiscussionSocialActivitiesService;
use Illuminate\Http\Request;

class DiscussionController extends AppBaseController
{
    private $discussionRepository;

    public function __construct(DiscussionRepository $discussionRepository)
    {
        $this->discussionRepository = $discussionRepository;
    }

    public function index($component, $slug, Request $request)
    {
        try {
            if (!in_array($component, ['lab', 'project', 'challenge', 'challenge-path'])) {
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            $checkComponentBasedOnSlug = UtilityHelper::checkComponentSlugExistOrNot($component, $slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.slug_not_found'), 404);
            }
            $getComponentId = $checkComponentBasedOnSlug->id;
            $list = $this->discussionRepository->index($component, $getComponentId, $request->sort_by);

            if ($list->count() > 0) {
                $response = [
                    'total_discussion_count' => UtilityHelper::getComponentTotalDiscussions($component, $getComponentId),
                    'list'                   => DiscussionResource::collection($list),
                ];

                return $this->sendResponse($response, __('responses.comments_lists_successfully'));
            }

            return $this->sendResponse([], __('responses.comments_lists_successfully'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addComment($component, $slug, AddCommentRequest $request)
    {
        try {
            if (!in_array($component, ['lab', 'project', 'challenge', 'challenge-path'])) {
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            $checkComponentBasedOnSlug = UtilityHelper::checkComponentSlugExistOrNot($component, $slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.slug_not_found'), 404);
            }
            $getComponentId = $checkComponentBasedOnSlug->id;
            $addComment = $this->discussionRepository->addComment($component, $request, $getComponentId);
            if ($addComment) {
                // LEARNING POINT NOTIFICATION
                LearningPointsHelper::sendBulkLearningPointNotification(
                    [auth()->id()],
                    data_get(data_get($addComment, 'comment_id') ? LearningPointsHelper::REPLY_TO_A_COMMENT : LearningPointsHelper::POST_A_COMMENT, 'type'),
                    data_get(data_get($addComment, 'comment_id') ? LearningPointsHelper::REPLY_TO_A_COMMENT : LearningPointsHelper::POST_A_COMMENT, 'points')
                );

                return $this->sendResponse(DiscussionResource::make($addComment), __('responses.add_comment_successfully'));
            }

            return $this->sendError(__('responses.add_comment_failed'), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function socialActivity($component, $slug, $id, $activity = null)
    {
        try {
            if (!in_array($component, ['lab', 'project', 'challenge', 'challenge-path'])) {
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            if (!in_array($activity, ['like', 'dislike'])) {
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            $checkComponentBasedOnSlug = UtilityHelper::checkComponentSlugExistOrNot($component, $slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.slug_not_found'), 404);
            }
            $getComponentId = $checkComponentBasedOnSlug->id;
            $checkCommentIdExistsOrNot = DiscussionService::checkCommentIdExistsOrNot($id, $getComponentId);
            if (!$checkCommentIdExistsOrNot) {
                return $this->sendError(__('responses.not_exists_id'), 422);
            }
            switch ($activity) {
                case 'like':
                    $checkLikedOrNot = DiscussionSocialActivitiesService::checkLikeOrDislikeComment($activity, $id);
                    if ($checkLikedOrNot) {
                        $like = $this->discussionRepository->unLikeOrUnDisLikeComponent($activity, $id);
                        $message = __('responses.unlike_successfully');
                    } else {
                        $like = $this->discussionRepository->likeDislike($activity, $id);
                        $message = __('responses.like_successfully');
                    }
                    if ($like) {
                        return $this->sendResponse(DiscussionResource::make($like), $message);
                    }
                    break;
                case 'dislike':
                    $checkDisLikedOrNot = DiscussionSocialActivitiesService::checkLikeOrDislikeComment($activity, $id);
                    if ($checkDisLikedOrNot) {
                        $dislike = $this->discussionRepository->unLikeOrUnDisLikeComponent($activity, $id);
                        $message = __('responses.un_dislike_successfully');
                    } else {
                        $dislike = $this->discussionRepository->likeDislike($component, $id);
                        $message = __('responses.dislike_successfully');
                    }
                    if ($dislike) {
                        return $this->sendResponse(DiscussionResource::make($dislike), $message);
                    }
                default:
                    return $this->sendError(__('responses.handler_bad_request'), 400);
                    break;
            }

            return $this->sendError(__('responses.handler_bad_request'), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteComment($component, $slug, $id)
    {
        try {
            $checkComponentBasedOnSlug = UtilityHelper::checkComponentSlugExistOrNot($component, $slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.slug_not_found'), 404);
            }
            $getComponentId = $checkComponentBasedOnSlug->id;
            $checkCommentId = DiscussionService::checkCommentIdExistsOrNot($id, $getComponentId);
            if (!$checkCommentId) {
                return $this->sendError(__('responses.not_exists_id'), 422);
            }
            $delete = $this->discussionRepository->deleteComment($id);
            if ($delete) {
                return $this->sendResponse([], __('responses.delete_successfully'));
            }

            return $this->sendError(__('responses.send_error'), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
