<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    // Pastikan kedua sisi adalah string
    return (string) $user->id === (string) $id;
});
