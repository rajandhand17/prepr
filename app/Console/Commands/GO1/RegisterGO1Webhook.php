<?php

namespace App\Console\Commands\GO1;

use App\Services\GO1\WebhookService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RegisterGO1Webhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'register:go1-webhook {--url=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command add webhook url to go1 which helps us to track progress of go1 based on events.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $webhook = new WebhookService();
            $webhook->registerWebhookToGO1($this->option('url') ?? '');
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
