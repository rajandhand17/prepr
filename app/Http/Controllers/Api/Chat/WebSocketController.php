<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\AppBaseController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Pusher\Pusher;
use Pusher\PusherException;

class WebSocketController extends AppBaseController
{

    /**
     * @throws PusherException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function auth(): string
    {
        $pusherConfig = config('broadcasting.connections.pusher');
        $pusher = new Pusher($pusherConfig['key'], $pusherConfig['secret'], $pusherConfig['app_id'], $pusherConfig['options']);

        return $pusher->authenticateUser(request()->get('socket_id'), auth()->user()->toArray());
    }
}
