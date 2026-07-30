<?php

// namespace App\Console\Commands;

// use App\Models\Jadwal;
// use App\Events\JadwalReminderEvent;
// use App\Models\BankSampah\JadwalPelaksanaan;
// use Illuminate\Console\Command;

// class SendJadwalReminder extends Command
// {
//     protected $signature = 'jadwal:reminder';
//     protected $description = 'Broadcast pengingat jadwal pelaksanaan hari ini via Pusher';

//     public function handle()
//     {
//         $jadwalHariIni = JadwalPelaksanaan::whereDate('tanggal_setoran', today())
//             ->with('user_detail')->whereHas('user_detail', function ($query) {

//                 $query->where('status', 'Disetujui');
//             })
//             ->get();

//         if ($jadwalHariIni->isEmpty()) {
//             $this->info('Tidak ada jadwal pelaksanaan hari ini.');
//             return;
//         }

//         foreach ($jadwalHariIni as $jadwal) {
//             broadcast(new JadwalReminderEvent($jadwal, $jadwal->rt_id));
//         }

//         $this->info("Reminder dikirim untuk {$jadwalHariIni->count()} jadwal.");
//     }
// }
