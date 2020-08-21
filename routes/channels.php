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

Broadcast::channel('message.{receiverid}', function ($user, $receiverid) {
    return auth()->check();
});

Broadcast::channel('add.queue', function ($user) {
    return auth()->check();
});

Broadcast::channel('remove.pool', function ($user) {
    return auth()->check();
});

Broadcast::channel('restore.pool', function ($user) {
    if (auth()->check()) {
        return $user->client ? $user->client->load('user') : $user;
    }
});

Broadcast::channel('accepted.by', function ($user) {
    return auth()->check();
});

Broadcast::channel('verify.{id}', function ($user, $id) {
    return auth()->check();
});

Broadcast::channel('verified', function ($user) {
    return auth()->check();
});

Broadcast::channel('newpooluser', function ($user) {
    return auth()->check();
});
