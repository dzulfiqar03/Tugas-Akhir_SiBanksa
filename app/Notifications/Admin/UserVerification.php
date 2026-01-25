<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class UserVerification extends Notification implements ShouldQueue
{
    use Queueable;

    public $nasabahId;
    public $message;
    public $url;

    public function __construct($nasabahId)
    {
        $this->nasabahId = $nasabahId;
        $this->message = "Pengajuan Akun telah diverifikasi!";
        $this->url = url("/Warga/dashboard");
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
            'id_user' => $this->nasabahId
        ];
    }

    // Data yang dikirim realtime ke Reverb
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => $this->message,
            'url' => $this->url,
            'id_user' => $this->nasabahId
        ]);
    }

    // Nama event di JavaScript akan menjadi 'SetoranDiverifikasiNotification'
    public function broadcastType(): string
    {
        return 'PengajuanDiverifikasiNotifikasi';
    }
}
