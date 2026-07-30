<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    // Pastikan kedua sisi adalah string
    return (string) $user->id === (string) $id;
});

// Broadcast::channel('rt.{rtId}', function ($user, $rtId) {
//     return $user->user_detail === (int) $rtId || $user->role === 'ketua_rw';
// });
