<?php

namespace App\Http\Resources\Manage\Lab;

use Illuminate\Http\Resources\Json\JsonResource;

class LabModeResource extends JsonResource
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
            $labModes = collect(['4'=>'team', '5'=>'individual']);
            foreach ($this->all() as $labMode) {
                if ($labModes->has($labMode)) {
                    $modeArray['id'] = $labMode;
                    $modeArray['title'] = $labModes->get($labMode);
                    $modeArrayData[] = $modeArray;
                }
            }
        }

        return $modeArrayData;
    }
}
