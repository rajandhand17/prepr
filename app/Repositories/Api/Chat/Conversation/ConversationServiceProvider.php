<?php

namespace App\Repositories\Api\Chat\Conversation;

use Illuminate\Support\ServiceProvider;

class ConversationServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->app->bind(ConversationInterface::class, ConversationRepository::class);
    }
}
