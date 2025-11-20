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

Broadcast::routes(['middleware' => ['auth:api', 'AuthenticateBroadcast']]);

// Private channel for Doctors
Broadcast::channel('Doctor.{id}', function ($user, $id) {
    if ($user instanceof Doctor) {
        return (int) $user->id === (int) $id;
    }
    return false;
});

// Private channel for Clients
Broadcast::channel('Client.{id}', function ($user, $id) {
    if ($user instanceof Client) {
        return (int) $user->id === (int) $id;
    }
    return false;
});

// Private channel for Admins
Broadcast::channel('Admin.{id}', function ($user, $id) {
    Log::info('🚀 OK', ["user" => $user]);
    if ($user instanceof App\Models\Admin) {
        return (int) $user->id === (int) $id;
    }
    return false;
});

Broadcast::channel('Admins', function ($user) {
    Log::info('🚀 OK', ["user" => $user]);
    return $user instanceof App\Models\Admin;
});

// Public channel for system-wide notifications (optional)
Broadcast::channel('notifications.public', callback: function ($user) {
    return true; // همه می‌تونن بشنون
});
