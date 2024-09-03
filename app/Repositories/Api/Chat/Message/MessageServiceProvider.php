<?php

namespace App\Repositories\Api\Chat\Message;

use Illuminate\Support\ServiceProvider;

class MessageServiceProvider extends ServiceProvider
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
        $this->app->bind(MessageInterface::class, MessageRepository::class);
    }
}
