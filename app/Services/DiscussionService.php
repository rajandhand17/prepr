<?php

namespace App\Services;

use App\Helpers\FileUploadHelper;
use App\Models\Discussion;
use DB;

class DiscussionService
{
    public function index($component, $moduleId, $sortBy)
    {
        try {
            $moduleType = Config('constants.discussion_module_type.'.$component);
            $getComments = Discussion::whereNull('comment_id')
                ->where('module_id', $moduleId)
                ->where('module_type', $moduleType);
            $getComments = self::filterCommentsList($getComments, $sortBy);
            $getComments = $getComments->get();
            if ($getComments) {
                return $getComments;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function filterCommentsList($getComments, $sortBy)
    {
        try {
            if ($sortBy == 'recent') {
                $getComments = $getComments->orderBy('created_at', 'DESC');
            }
            if ($sortBy == 'top') {
                $getComments = $getComments->withCount(['liked_by as likes_count' => function ($query) {
                    return $query;
                }])->withCount(['disliked_by as dislikes_count' => function ($query) {
                    return $query;
                }])->orderByDesc('likes_count')
                    ->orderByDesc('dislikes_count');
            }

            return $getComments;
        } catch (\Exception $e) {
            dd($e);

            return false;
        }
    }

    public function addComment($component, $request, $getComponentId)
    {
        try {
            $attachmentPath = null;
            if ($request->file('attachments') && $request->file('attachments') !== null) {
                $attachments = $request->file('attachments');
                $attachmentPath = FileUploadHelper::uploadImageToS3($attachments, 'discussion');
                if ($attachmentPath == false) {
                    return false;
                }
            }
            DB::beginTransaction();
            $addComment = new Discussion();
            $addComment->user_id = auth()->user()->id;
            $addComment->module_id = $getComponentId;
            $addComment->module_type = config('constants.discussion_module_type.'.$component);
            $addComment->comments = $request->comment ? $request->comment : null;
            $addComment->attachment = $attachmentPath;
            $addComment->comment_id = isset($request->comment_id) ? $request->comment_id : null;
            $addComment->save();
            DB::commit();

            return $addComment;
        } catch(\Exception $e) {
            DB::rollback();

            return false;
        }
    }

    public function deleteDiscussion($commentId)
    {
        try {
            $deletedCommentIds = Discussion::where('id', $commentId)
                ->orWhere('comment_id', $commentId)
                ->pluck('id');
            Discussion::whereIn('id', $deletedCommentIds)
                ->delete();

            return true;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function checkCommentIdExistsOrNot($id, $commentId)
    {
        try {
            $checkId = Discussion::where(['id'=>$id, 'module_id'=>$commentId])->first();
            if ($checkId) {
                return $checkId;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
