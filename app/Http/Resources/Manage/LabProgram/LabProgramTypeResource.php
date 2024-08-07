<?php

namespace App\Http\Resources\Manage\LabProgram;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class LabProgramTypeResource extends JsonResource
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
            $labProgramTypes = collect(['0'=>'assess','1'=>'onboard','2'=>'engage','3'=>'grow']);
            foreach ($this->all() as $labProgramType) {
                if ($labProgramTypes->has($labProgramType)) {
                    $typeArray['id']    = $labProgramType;
                    $typeArray['title'] = $labProgramTypes->get($labProgramType);
                    $typeArrayData[]    = $typeArray;
                }
            }
        }
        return $typeArrayData;
    }
}