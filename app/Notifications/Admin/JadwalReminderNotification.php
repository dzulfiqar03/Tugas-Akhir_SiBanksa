<?php

// namespace App\Notifications;

// use Illuminate\Bus\Queueable;
// use Illuminate\Notifications\Notification;
// use Illuminate\Notifications\Messages\BroadcastMessage;

// class JadwalReminderNotification extends Notification
// {
//     use Queueable;

//     public function __construct(protected $jadwal) {}

//     public function via($notifiable)
//     {
//         return ['database']; // + 'database' kalau mau disimpan juga di tabel notifications
//     }

//     public function toBroadcast($notifiable)
//     {
//         return new BroadcastMessage([
//             'id' => $this->id, // otomatis ada dari base Notification
//             'title' => 'Pengingat Jadwal Pelaksanaan',
//             'message' => "Jadwal hari ini: {$this->jadwal->tanggal_setoran->format('Y-m-d')}",
//             'body' => "$this->jadwal->keterangan",
//             'url' => null, // bisa diisi kalau mau ada link ke halaman tertentu
//         ]);
//     }

//     // kalau pakai 'database' channel juga
//     public function toArray($notifiable)
//     {
//         return [
//             'message' => "Jadwal hari ini: {$this->jadwal->keterangan}",
//             'url' => '/jadwal/' . $this->jadwal->id,
//         ];
//     }
// }
