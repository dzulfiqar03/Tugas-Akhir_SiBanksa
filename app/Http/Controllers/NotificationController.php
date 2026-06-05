<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function readNotif($id)
    {
        auth()->user()->notifications()->findOrFail($id)->markAsRead();
        return redirect()->back()->with('message', 'Notif berhasil dibaca');
    }

    public function readAllNotif(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return back();
    }
}
