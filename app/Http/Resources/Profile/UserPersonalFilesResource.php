<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPersonalFilesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $array = explode('/', $this->name);
        if ($this->public == '1') {
            $public = 'yes';
        } else {
            $public = 'no';
        }
        if (!auth('api')->check() && $public == 'no' || $public == 'no' && auth('api')->user()->id != $this->user_id) {
            return [];
        } else {
            return [
                'id'            => $this->id,
                'path'          => $this->name,
                'name'          => end($array),
                'original_name' => $this->original,
                'public'        => $public,
            ];
        }
    }
}
