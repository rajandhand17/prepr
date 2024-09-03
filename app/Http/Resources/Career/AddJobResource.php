<?php

namespace App\Http\Resources\Career;

use App\Services\JobTitleService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddJobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $job = JobTitleService::getJobBasedOnId($this->job_id);

        return [
            'id'     => $this->id,
            'skill'  => $job,
            'pinned' => $this->pinned == '1' ? 'yes' : 'no',
        ];
    }
}
