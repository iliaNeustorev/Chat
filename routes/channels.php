<?php

use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
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

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('translation.{channel}', function ($user, Channel $channel) {
    $ownerId = $channel->owner_id;
    $role = $user->roles->first()->name;
    if($user->id != $ownerId){
        $ownerUser = User::findOrfail($ownerId);
    }
    if($user->id == $ownerId 
        || $ownerUser->lawyer_id == $user->id 
        || $ownerUser->manager_id == $user->id 
        || $role == "Admin") {
            return true;
    } elseif ($user->id != $ownerId 
        && $ownerUser->lawyer_id == 0 
        && $ownerUser->manager_id == 0 
        && $role == 'Support') {
            return true;
    }
});

Broadcast::channel('channel-support', function ($user) {
    $role = $user->roles->first()->name;
    return $role == 'Support' || $role == 'Admin';
});

// Broadcast::channel('translation.{channelId}', function ($user, $channelId) {
//    $role = $user->roles->first()->name;
//    if($user->channels->contains($channelId) || $role == "Admin") {
//         return['name' => $user->name, 'role' => $role];
//    }; 
//       return $user->channels->contains($channelId) || $role == "Admin";
// });