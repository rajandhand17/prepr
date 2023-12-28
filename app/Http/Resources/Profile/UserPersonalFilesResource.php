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

        return [
            'id'        => $this->id,
            'path'      => $this->original,
            'name'      => end($array),
        ];
    }
}
