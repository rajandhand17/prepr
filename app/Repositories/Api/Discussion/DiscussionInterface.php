<?php

namespace App\Repositories\Api\Discussion;

interface DiscussionInterface
{
    public function index($component, $moduleId, $sortBy);

    public function addComment($component, $request, $getComponentId);

    public function deleteComment($commentId);

    public function likeDislike($action, $comment_id);

    public function unLikeOrUnDisLikeComponent($likeOrDislike, $comment_id);
}
