<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('users.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('message.conversation.{id}', function ($user, $id) {
    $conversation = $user->conversations()->where('id', $id)->first();

    if ($conversation) {
        return true;
    }

    return false;
});

Broadcast::channel('chat', function ($user) {
    return ['id' => $user->id, 'email' => $user->email, 'full_name' => $user->full_name, 'username' => $user->username];
});
