<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ChatSendNotif extends Notification implements ShouldBroadcastNow
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
        return ['database', 'broadcast', WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Pesan Baru')
            ->icon('/icon.png')
            ->body($this->message)
            ->data(['url' => $this->url])
            ->options(['TTL' => 1000]);
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
    return (new BroadcastMessage([
        'message' => $this->message,
        'url' => $this->url,
        'id_user' => $this->rwId
    ]))->onConnection('sync');
}


}
