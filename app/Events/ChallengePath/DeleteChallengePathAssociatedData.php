<?php

namespace App\Events\ChallengePath;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteChallengePathAssociatedData
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $challengePathId;

    /**
     * Create a new event instance.
     */
    public function __construct($challengePathId)
    {
        $this->challengePathId = $challengePathId;
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
