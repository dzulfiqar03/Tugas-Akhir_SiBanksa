<?php

use App\Http\Controllers\Admin\BankSampah\ArchiverReportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\BankSampah\DataNasabahController;
use App\Http\Controllers\Admin\BankSampah\DataSampahController;
use App\Http\Controllers\Admin\BankSampah\DataTransaksiController;
use App\Http\Controllers\Admin\BankSampah\JadwalPelaksanaanController;
use App\Http\Controllers\Admin\BankSampah\KepengurusanController;
use App\Http\Controllers\Admin\BankSampah\PencatatanController;
use App\Http\Controllers\Admin\BankSampah\TrackingSetoranController;
use App\Http\Controllers\Admin\BankSampah\UserChatController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KetuaRW\JadwalController;
use App\Http\Controllers\Admin\KetuaRW\KelolaBankSampahController;
use App\Http\Controllers\Admin\KetuaRW\KetuaRWChatController;
use App\Http\Controllers\Admin\KetuaRW\PelaporanController;
use App\Http\Controllers\Admin\PreferenceController;
use App\Http\Controllers\Admin\Warga\DataTransaksiController as WargaDataTransaksiController;
use App\Http\Controllers\Admin\Warga\JadwalPenyetoranController;
use App\Http\Controllers\Admin\Warga\TrackingSetoranController as WargaTrackingSetoranController;
use App\Http\Controllers\Admin\Warga\WargaChatController;
use App\Http\Controllers\DocumentArchiverController;
use App\Http\Controllers\EvidenceArchiverController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\System\InternetConnController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        // Jika sudah login, arahkan ke dashboard masing-masing berdasarkan role
        $role = Auth::user()->user_detail->roles->role; // Sesuaikan dengan kolom role di DB anda

        if ($role == 'Ketua RW') {
            return redirect()->route('rw.dashboard');
        } elseif ($role == 'Bank Sampah') {
            return redirect()->route('dashboard');
        } elseif ($role == 'Warga') {
            return redirect()->route('warga.dashboard');
        }
    }
});

Route::get('/testInternet', [InternetConnController::class, 'checkConnection'])->name('check-internet');

Route::middleware(['conn'])->group(function () {

    Route::middleware(['auth'])->group(function () {

        Route::post('/notifications/{id}/read', [NotificationController::class, 'readNotif'])->name('notifications.read');
        Route::post('/notifications/readAll', [NotificationController::class, 'readAllNotif'])->name('notifications.readAll');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::post('/profile/edit', [ProfileController::class, 'editAll'])->name('profile-edit');

        Route::middleware(['verified'])->group(function () {

            Route::get('/preference', [PreferenceController::class, 'index'])->name('preference');

            Route::middleware(['roles:Ketua RW'])->group(function () {
                Route::get('/KetuaRW/dashboard', [DashboardController::class, 'index'])->name('rw.dashboard');

                Route::get('/KetuaRW/Kelola-Bank-Sampah', [KelolaBankSampahController::class, 'index'])->name('rw.data-kelola');
                Route::get('/KetuaRW/bank-sampah/detail/{id}', [KelolaBankSampahController::class, 'show'])->name('rw.show-banksampah');
                Route::post('/KetuaRW/bank-sampah/create', [KelolaBankSampahController::class, 'store'])->name('rw.add-banksampah');
                Route::put('/KetuaRW/bank-sampah/update/{id}', [KelolaBankSampahController::class, 'update'])->name('rw.update-banksampah');
                Route::delete('/KetuaRW/bank-sampah/delete/{id}', [KelolaBankSampahController::class, 'destroy'])->name('rw.delete-banksampah');

                Route::get('/KetuaRW/Jadwal', [JadwalPelaksanaanController::class, 'show'])->name('rw.jadwal-pelaksanaan');
                Route::get('/KetuaRW/jadwal-bankSampah/detail/{id}', [JadwalController::class, 'show'])->name('rw.show-jadwalBankSampah');


                Route::get('/KetuaRW/nasabah', [DataNasabahController::class, 'index'])->name('rw.data-nasabah');
                Route::get('/KetuaRW/tracking', [TrackingSetoranController::class, 'index'])->name('rw.data-tracking');
                Route::get('/KetuaRW/transaksi', [DataTransaksiController::class, 'index'])->name('rw.data-transaksi');
                Route::get('/KetuaRW/pencatatan', [PencatatanController::class, 'index'])->name('rw.pencatatan-setoran');

                Route::get('/KetuaRW/pelaporan', [PelaporanController::class, 'index'])->name('data-pelaporanBankSampah');

                Route::post('/KetuaRW/{id}/send-reminder', action: [KelolaBankSampahController::class, 'sendReminder'])->name('banksampah.send-reminder');
                Route::post('/KetuaRW/{id}/update-transaction', action: [PelaporanController::class, 'update'])->name('rw.open-transaction');

                Route::get('/KetuaRW/chat', [KetuaRWChatController::class, 'index'])->name('rw.chat');
                Route::post('/KetuaRW/chat/create{id}', [KetuaRWChatController::class, 'store'])->name('rw.add-chat');
                Route::put('/KetuaRW/chat/update/{id}', [KetuaRWChatController::class, 'update'])->name('rw.update-chat');
                Route::delete('/KetuaRW/chat/delete/{id}', [KetuaRWChatController::class, 'destroy'])->name('rw.delete-chat');
                Route::delete('/KetuaRW/chat/deleteChat/{id}', [KetuaRWChatController::class, 'deleteRoomChat'])->name('rw.delete-roomChat');

                Route::put('/KetuaRW/chat/read{id}', [KetuaRWChatController::class, 'readChat'])->name('rw.read-chat');
                Route::post('/KetuaRW/chatbot/create{id}', [KetuaRWChatController::class, 'store'])->name('rw.add-chatbot');
            });

            Route::middleware(['roles:Bank Sampah'])->group(function () {
                Route::get('/bank-sampah/dashboard', [DashboardController::class, 'index'])->name('dashboard');

                Route::get('/bank-sampah/Jadwal', [JadwalPelaksanaanController::class, 'index'])->name('jadwal-pelaksanaan');
                Route::post('/bank-sampah/Jadwal/Create', [JadwalPelaksanaanController::class, 'store'])->name('add-jadwalBankSampah');
                Route::put('/bank-sampah/Jadwal/Update/{Jadwal}', [JadwalPelaksanaanController::class, 'update'])->name('update-jadwalBankSampah');
                Route::delete('/bank-sampah/Jadwal/Delete/{Jadwal}', [JadwalPelaksanaanController::class, 'destroy'])->name('delete-jadwalBankSampah');

                Route::get('/bank-sampah/Sampah', [DataSampahController::class, 'index'])->name('data-sampah');
                Route::post('/bank-sampah/Sampah/Create', [DataSampahController::class, 'store'])->name('add-sampah');
                Route::put('/bank-sampah/Sampah/Update/{id}', [DataSampahController::class, 'update'])->name('update-sampah');
                Route::delete('/bank-sampah/Sampah/Delete/{id}', [DataSampahController::class, 'destroy'])->name('delete-sampah');


                Route::get('/bank-sampah/nasabah', [DataNasabahController::class, 'index'])->name('data-nasabah');
                Route::get('/bank-sampah/nasabah/detail/{id}', [DataNasabahController::class, 'show'])->name('show-nasabah');
                Route::post('/bank-sampah/nasabah/create', [DataNasabahController::class, 'store'])->name('add-nasabah');
                Route::put('/bank-sampah/nasabah/update/{id}', [DataNasabahController::class, 'update'])->name('update-nasabah');
                Route::delete('/bank-sampah/nasabah/delete/{id}', [DataNasabahController::class, 'destroy'])->name('delete-nasabah');


                Route::get('/bank-sampah/kepengurusan', [KepengurusanController::class, 'index'])->name('data-kepengurusan');
                Route::get('/bank-sampah/kepengurusan/detail/{id}', [KepengurusanController::class, 'show'])->name('show-kepengurusan');
                Route::post('/bank-sampah/kepengurusan/create', [KepengurusanController::class, 'store'])->name('add-kepengurusan');
                Route::put('/bank-sampah/kepengurusan/update/{id}', [KepengurusanController::class, 'update'])->name('update-kepengurusan');
                Route::delete('/bank-sampah/kepengurusan/delete/{id}', [KepengurusanController::class, 'destroy'])->name('delete-kepengurusan');


                Route::post('/nasabah/{id}/send-reminder', action: [DataNasabahController::class, 'sendReminder'])->name('nasabah.send-reminder');

                Route::get('/bank-sampah/tracking', [TrackingSetoranController::class, 'index'])->name('data-tracking');

                Route::get('/bank-sampah/transaksi', [DataTransaksiController::class, 'index'])->name('data-transaksi');
                Route::post('/bank-sampah/{id}/chat-transaction', action: [PelaporanController::class, 'sendChat'])->name('bs.chat-transaction');
                Route::post('/bank-sampah/transaksi/create', [DataTransaksiController::class, 'store'])->name('bs.add-transaction');
                Route::put('/bank-sampah/transaksi/update/{id}', [DataTransaksiController::class, 'update'])->name('bs.update-transaction');
                Route::delete('/bank-sampah/transaksi/delete/{id}', [DataTransaksiController::class, 'destroy'])->name('bs.delete-transaction');


                Route::get('/bank-sampah/pencatatan', [PencatatanController::class, 'index'])->name('pencatatan-setoran');
                Route::post('/bank-sampah/pencatatan/create', [PencatatanController::class, 'store'])->name('add-setoran');
                Route::get('/bank-sampah/pencatatan/detail/{id}', [PencatatanController::class, 'show'])->name('show-pencatatan');
                Route::delete('/bank-sampah/pencatatan/delete/{id}', [PencatatanController::class, 'destroy'])->name('delete-pencatatan');


                Route::get('/bank-sampah/pelaporan', [ArchiverReportController::class, 'index'])->name('data-pelaporanRW');

                Route::post('/bank-sampah/document/create', [DocumentArchiverController::class, 'store'])->name('add-document');
                Route::put('/bank-sampah/document/update/{id}', [DocumentArchiverController::class, 'update'])->name('update-document');
                Route::delete('/bank-sampah/document/delete/{id}', [DocumentArchiverController::class, 'destroy'])->name('delete-document');

                Route::post('/bank-sampah/evidence/create', [EvidenceArchiverController::class, 'store'])->name('add-evidence');
                Route::put('/bank-sampah/evidence/update/{id}', [EvidenceArchiverController::class, 'update'])->name('update-evidence');
                Route::delete('/bank-sampah/evidence/delete/{id}', [EvidenceArchiverController::class, 'destroy'])->name('delete-evidence');

                Route::post('/lapor-setoran/{id}/send-reminder', action: [PelaporanController::class, 'sendReminder'])->name('laporsetoran.send-reminder');

                Route::get('/bank-sampah/chat', [UserChatController::class, 'index'])->name('banksampah.chat');
                Route::post('/bank-sampah/chat/create{id}', [UserChatController::class, 'store'])->name('bs.add-chat');
                Route::put('/bank-sampah/chat/update/{id}', [UserChatController::class, 'update'])->name('bs.update-chat');
                Route::delete('/bank-sampah/chat/delete/{id}', [UserChatController::class, 'destroy'])->name('bs.delete-chat');
                Route::delete('/bank-sampah/chat/deleteChat/{id}', [UserChatController::class, 'deleteRoomChat'])->name('bs.delete-roomChat');

                Route::put('/bank-sampah/chat/read{id}', [UserChatController::class, 'readChat'])->name('bs.read-chat');
                Route::post('/bank-sampah/chatbot/create{id}', [UserChatController::class, 'store'])->name('bs.add-chatbot');
            });

            Route::middleware(['roles:Warga'])->group(function () {
                Route::get('/Warga/dashboard', [DashboardController::class, 'index'])->name('warga.dashboard');
                Route::get('/Warga/transaksi', [WargaDataTransaksiController::class, 'index'])->name('warga.data-transaksi');

                Route::get('/Warga/JanjiSetor', [JadwalPenyetoranController::class, 'index'])->name('warga.janji-setor');
                Route::post('/Warga/JanjiSetor/Create', [JadwalPenyetoranController::class, 'store'])->name('warga.add-janjiSetor');
                Route::put('/Warga/JanjiSetor/Update/{Jadwal}', [JadwalPenyetoranController::class, 'update'])->name('warga.update-janjiSetor');
                Route::delete('/Warga/JanjiSetor/Delete/{Jadwal}', [JadwalPenyetoranController::class, 'destroy'])->name('warga.delete-janjiSetor');


                Route::get('/Warga/tracking', [WargaTrackingSetoranController::class, 'index'])->name('warga.tracking-setoran');

                Route::get('/Warga/chat', [WargaChatController::class, 'index'])->name('warga.chat');
                Route::post('/Warga/chat/create{id}', [WargaChatController::class, 'store'])->name('warga.add-chat');
                Route::put('/Warga/chat/update/{id}', [WargaChatController::class, 'update'])->name('warga.update-chat');
                Route::delete('/Warga/chat/delete/{id}', [WargaChatController::class, 'destroy'])->name('warga.delete-chat');
                Route::delete('/Warga/chat/deleteChat/{id}', [WargaChatController::class, 'deleteRoomChat'])->name('warga.delete-roomChat');

                Route::put('/Warga/chat/read{id}', [WargaChatController::class, 'readChat'])->name('warga.read-chat');
                Route::post('/Warga/chatbot/create{id}', [WargaChatController::class, 'store'])->name('warga.add-chatbot');
            });
        });
    });

    require __DIR__ . '/auth.php';

    Route::redirect('/', 'login');
});
