<?php

namespace App\Http\Resources\Master;

use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'language' => $this->language,
            'challenge' => $this->challenge,
            'lab'=>$this->lab,
            'resource'=>$this->resource,
            'tag'=>$this->tag,
            'fr_CA_tag'=>$this->fr_CA_tag,
            'tag_image'=>$this->tag_image,
            'fr_CA_tag_image'=>$this->fr_CA_tag_image,
            'category'=>$this->category,
        ];
    }
}
