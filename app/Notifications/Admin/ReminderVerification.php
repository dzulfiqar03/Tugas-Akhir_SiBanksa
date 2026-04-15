<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ReminderVerification extends Notification implements ShouldQueue
{
    use Queueable;

    public $wargaId;
    public $message;
    public $url;

    public function __construct($wargaId, $message)
    {
        $this->wargaId = $wargaId;
        $this->message = $message;
        $this->url = url("/profile");
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
        'body'    => $this->message,
            'url' => $this->url,
            'id_user' => $this->wargaId
        ]);
    }

    // Nama event di JavaScript akan menjadi 'SetoranDiverifikasiNotification'
    public function broadcastType(): string
    {
        return 'ReminderVerificationNotifikasi';
    }
}
