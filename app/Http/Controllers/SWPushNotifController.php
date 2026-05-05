<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Illuminate\Support\Facades\Validator;
class SWPushNotifController extends Controller
{


public static function sendPush($user, $title, $message, $url = '/')
{
    $auth = [
        'VAPID' => [
            'subject' => 'mailto:admin@sibanksa.com',
            'publicKey' => env('VAPID_PUBLIC_KEY'),
            'privateKey' => env('VAPID_PRIVATE_KEY'),
        ],
    ];

    $webPush = new WebPush($auth);
    $subscriptions = $user->pushSubscriptions; // Mengambil hasMany dari model User

    foreach ($subscriptions as $sub) {
        $webPush->queueNotification(
            Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->public_key,
                'authToken' => $sub->auth_token,
            ]),
            json_encode([
                'title' => $title,
                'body' => $message,
                'url' => $url
            ])
        );
    }

    foreach ($webPush->flush() as $report) {
        if (!$report->isSuccess()) {
            \Log::error("WebPush Error: {$report->getReason()}");
        }
    }
}

public function store(Request $request)
{
    // Jangan pakai $request->validate atau $this->validate
    // Gunakan Validator facade agar lebih aman
    $validator = Validator::make($request->all(), [
        'endpoint' => 'required',
        'keys.p256dh' => 'required',
        'keys.auth' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    auth()->user()->pushSubscriptions()->updateOrCreate(
        ['endpoint' => $request->endpoint],
        [
            'public_key' => $request->keys['p256dh'],
            'auth_token' => $request->keys['auth']
        ]
    );

    return response()->json(['message' => 'Subscription stored']);
}

}
