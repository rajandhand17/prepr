<?php

namespace App\Events\ChallengeTemplate;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteChallengeTemplateAssociatedData
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;
    public $challengeTemplateId;

    /**
     * Create a new event instance.
     */
    public function __construct($challengeTemplateId)
    {
        $this->challengeTemplateId = $challengeTemplateId;
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
