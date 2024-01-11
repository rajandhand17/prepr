<?php

namespace App\Events\Project;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteProjectAssociatedData
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;
    public $project_Id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($project_Id)
    {
        $this->project_Id = $project_Id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
