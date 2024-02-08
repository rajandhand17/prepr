<?php

namespace App\Jobs;

use App\Events\Chat\ConversationCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessConversationCreated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $conversation, public $userId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        broadcast(new ConversationCreated($this->conversation, $this->userId))->toOthers();
    }
}
