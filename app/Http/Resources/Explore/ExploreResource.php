<?php

namespace App\Http\Resources\Explore;

use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabService;
use App\Services\Manage\ResourceCollectionService;
use App\Services\Manage\ResourceGroupService;
use App\Services\Manage\ResourceModuleService;
use App\Services\ProjectService;
use App\Services\Public\ChallengePathService as PublicChallengePathService;
use App\Services\Public\LabProgramService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExploreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $details = [];

        switch($this->module_type) {
            case '0':   // Labs
                $moduleDetails = LabService::getLabBasedOnId($this->module_id);
                break;
            case '1': // Lab Program
                $moduleDetails = LabProgramService::getLabProgramBasedOnId($this->module_id);
                break;
            case '2': // Challenge
                $moduleDetails = ChallengeService::getChallengeBasedOnId($this->module_id);
                break;
            case '3': // Challenge Path
                $moduleDetails = PublicChallengePathService::getChallengePathBasedOnId($this->module_id);
                break;
            case '4': // Resource Module
                $moduleDetails = ResourceModuleService::getResourceModuleBasedOnId($this->module_id);
                break;
            case '5': // Resource Collection
                $moduleDetails = ResourceCollectionService::getResourceCollectionBasedOnId($this->module_id);
                break;
            case '6': // Resource Group
                $moduleDetails = ResourceGroupService::getResourceGroupBasedOnId($this->module_id);
                break;
            case '7': // Projects
                $moduleDetails = ProjectService::getProjectBasedOnId($this->module_id);
                break;
        }

        return [
            'id'                     => $this->id,
            'title'                  => $this->title,
            'description'            => $this->description,
            'module_id'              => $this->module_id,
            'module_type'            => config('constants.module_type_id.'.$this->module_type),
            'media'                  => $this->media,
            'module_slug'            => $moduleDetails!==null ? $moduleDetails->slug : null,
            'button_text'            => $this->button_text,
        ];
    }
}
