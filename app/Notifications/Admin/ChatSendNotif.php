<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ChatSendNotif extends Notification implements ShouldQueue
{
    use Queueable;

    public $rwId;
    public $message;
    public $url;

    public function __construct($rwId, $message, $uri)
    {
        $this->rwId = $rwId;
        $this->message = $message;
        $this->url = url($uri);
    }

    // Simpan ke Database & Kirim ke Broadcast (Reverb)
    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    // Data yang disimpan di tabel 'notifications'
    public function toDatabase($notifiable): array
    {
        return [
            'message' => $this->message,
            'url' => $this->url,
            'id_user' => $this->rwId
        ];
    }

    // Data yang dikirim realtime ke Reverb
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => $this->message,
            'url' => $this->url,
            'id_user' => $this->rwId
        ]);
    }

    public function broadcastType(): string
    {
        return 'RWChatNotifikasi';
    }
}
