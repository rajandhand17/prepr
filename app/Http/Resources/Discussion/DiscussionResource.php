<?php

namespace App\Http\Resources\Discussion;

use App\Http\Resources\User\UserResource;
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
        if($this->liked_by){
            $getLikedById=$this->liked_by->pluck('user_id');
            $getLikedByUser=UserService::getUserById($getLikedById);
        }
        if($this->disliked_by){
            $getDisLikedByUser=$this->disliked_by->pluck('user_id');
            $getDislikedByUser=UserService::getUserById($getDisLikedByUser);
        }
        $data = [
            "id"            =>$this->id,
            "module_type"   =>$discussionModuleType,
            "module"        =>$this->module_id,
            "comment"       =>$this->comments,
            "user_details"  =>UserDetailDiscussionResource::make($this->users),
            'comments_reply'=>DiscussionResource::collection($this->comments_reply),
            'liked_by'      =>UserDetailDiscussionResource::collection($getLikedByUser),
            'disliked_by'  =>UserDetailDiscussionResource::collection($getDislikedByUser),
        ];
        return $data;
    }
}
