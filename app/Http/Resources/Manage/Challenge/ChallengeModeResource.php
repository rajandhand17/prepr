<?php

namespace App\Http\Resources\Manage\Challenge;

use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeModeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $value = null;
        switch ($this->value) {
            case '4':
                $value = 'team';
                break;
            case '5':
                $value = 'individual';
                break;
        }

        return [
            'mode'          => $value,
        ];
    }
}
