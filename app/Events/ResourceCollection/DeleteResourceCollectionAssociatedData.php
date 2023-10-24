<?php

namespace App\Events\ResourceCollection;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteResourceCollectionAssociatedData
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $resourceCollectionId;

    /**
     * Create a new event instance.
     */
    public function __construct($resourceCollectionId)
    {
        $this->resourceCollectionId = $resourceCollectionId;
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
