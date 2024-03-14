<?php

namespace App\Events\Organization;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteOrganizationAssociatedData
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;
    public $organizationId;

    /**
     * Create a new event instance.
     */
    public function __construct($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
