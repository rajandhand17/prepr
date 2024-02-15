<?php

namespace App\Http\Resources\Discussion;

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
            'id'            => $this->id,
            'module_id'     => $this->module_id,
            'comments'      =>$this->comments,
            'comments_reply'=>$this->comments_reply,
        ];

        return $data;
    }
}
