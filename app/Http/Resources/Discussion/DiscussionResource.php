<?php

namespace App\Http\Resources\Discussion;

use App\Http\Resources\User\UserResource;
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
        $data = [
            "id"            =>$this->id,
            "module_type"   =>$this->module_type,
            "module_id"     =>$this->module_id,
            "comment"       =>$this->comments,
            "user_details"  =>UserResource::make($this->users),
            'comments_reply'=>DiscussionResource::collection($this->comments_reply),
        ];
        return $data;
    }
}
