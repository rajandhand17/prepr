<?php

namespace App\Events\ResourceGroup;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteResourceGroupAssociatedData
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;
    public $resourceGroupId;

    /**
     * Create a new event instance.
     */
    public function __construct($resourceGroupId)
    {
        $this->resourceGroupId = $resourceGroupId;
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
