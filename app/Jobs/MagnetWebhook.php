<?php

namespace App\Jobs;

use App\Helpers\MagnetHelper;
use App\Services\Manage\OrganizationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MagnetWebhook implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $lab, protected $updateType)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $organization = OrganizationService::getOrganizationExistBasedOnId($this->lab->organization_id);
        if ($organization) {
            if ($organization->magnet_community_id != null) {
                $params = [
                    'id'              => $this->lab->id,
                    'community_id'    => $organization->magnet_community_id,
                    'name'            => $this->lab->title,
                    'learning_status' => $this->updateType,
                    'slug'            => $this->lab->slug,
                    'description'     => $this->lab->description,
                    'provided_by'     => $organization->title,
                    'image'           => $this->lab->media,
                ];

                MagnetHelper::updateLearningContentOnMagnet($params);
            }
        }
    }
}
