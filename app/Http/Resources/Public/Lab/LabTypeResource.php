<?php

namespace App\Http\Resources\Public\Lab;

use Illuminate\Http\Resources\Json\JsonResource;

class LabTypeResource extends JsonResource
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
        $typeArrayData = [];
        if (!empty($this->all())) {
            $labTypes = collect(['0'=>'assess', '1'=>'onboard', '2'=>'engage', '3'=>'grow']);
            foreach ($this->all() as $labType) {
                if ($labTypes->has($labType)) {
                    $typeArray['id'] = $labType;
                    $typeArray['title'] = $labTypes->get($labType);
                    $typeArrayData[] = $typeArray;
                }
            }
        }

        return $typeArrayData;
    }
}
