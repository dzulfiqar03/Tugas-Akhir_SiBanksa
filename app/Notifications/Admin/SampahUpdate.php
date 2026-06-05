<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SampahUpdate extends Notification
{
    use Queueable;

      public $wargaId;
    public $message;
    public $url;

    public function __construct($wargaId, $message)
    {
        $this->wargaId = $wargaId;
        $this->message = $message;
        $this->url = url("Warga/dashboard");
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
            'id_user' => $this->wargaId
        ];
    }

    // Data yang dikirim realtime ke Reverb
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => $this->message,
            'url' => $this->url,
            'id_user' => $this->wargaId
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
