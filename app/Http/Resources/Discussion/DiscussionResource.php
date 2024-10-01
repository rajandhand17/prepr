<?php

namespace App\Http\Resources\Discussion;

use App\Services\DiscussionSocialActivitiesService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscussionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $discussionModuleType = array_search($this->module_type, config('constants.discussion_module_type'));
        $getLikedByUser = 0;
        $getDislikedByUser = 0;
        if ($this->liked_by) {
            $getLikedById = $this->liked_by->pluck('user_id');
            $getLikedByUser = UserService::getUserById($getLikedById)->count();
        }
        if ($this->disliked_by) {
            $getDisLikedByUser = $this->disliked_by->pluck('user_id');
            $getDislikedByUser = UserService::getUserById($getDisLikedByUser)->count();
        }
        $byMe = DiscussionSocialActivitiesService::checkLikedOrUnlikedBasedOnUser($this->id, auth()->user()->id);
        $data = [
            'id'              => $this->id,
            'comment'         => $this->comments,
            'likes'           => $getLikedByUser,
            'dislikes'        => $getDislikedByUser,
            'by_me'           => $byMe,
            'attachment'      => $this->getAttachments($this->attachment, $this->attachment_names),
            'user_details'    => UserResource::make($this->users),
            'comment_replies' => CommentReplies::collection($this->comments_reply),
            'created_at'      => $this->created_at,
        ];

        return $data;
    }

    private function getAttachments($attachments, $attachment_names)
    {
        if (!$attachments) {
            return [];
        }

        return array_map(function ($attachment, $name) {
            return [
                'name' => $name ?? $attachment,
                'link' => $attachment,
            ];
        }, $attachments, $attachment_names);
    }
}
