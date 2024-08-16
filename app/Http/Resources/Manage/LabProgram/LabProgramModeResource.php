<?php

namespace App\Http\Resources\Manage\LabProgram;

use Illuminate\Http\Resources\Json\JsonResource;

class LabProgramModeResource extends JsonResource
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
        $modeArrayData = [];
        if (!empty($this->all())) {
            $labProgramModes = collect(['4'=>'team', '5'=>'individual']);
            foreach ($this->all() as $labProgramMode) {
                if ($labProgramModes->has($labProgramMode)) {
                    $typeArray['id'] = $labProgramMode;
                    $typeArray['title'] = $labProgramModes->get($labProgramMode);
                    $modeArrayData[] = $typeArray;
                }
            }
        }

        return $modeArrayData;
    }
}
