<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\AppBaseController;
use Pusher\Pusher;

class WebSocketController extends AppBaseController
{

    public function auth(): string
    {
        try {
            $pusherConfig = config('broadcasting.connections.pusher');
            $pusher = new Pusher($pusherConfig['key'], $pusherConfig['secret'], $pusherConfig['app_id'], $pusherConfig['options']);

            return $pusher->authenticateUser(request()->get('socket_id'), auth()->user()->toArray());
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
