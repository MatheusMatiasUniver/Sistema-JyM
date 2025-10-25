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

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal público para status do kiosk
Broadcast::channel('kiosk-status', function () {
    return true; // Canal público, qualquer um pode ouvir
});

// Canal para eventos de registro de cliente
Broadcast::channel('client-registration', function () {
    return true; // Canal público para o kiosk
});