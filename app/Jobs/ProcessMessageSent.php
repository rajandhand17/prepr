<?php

namespace App\Jobs;

use App\Events\Chat\MessageSent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMessageSent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $chat, public $conversationId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        broadcast(new MessageSent($this->chat, $this->conversationId))->toOthers();
    }
}
