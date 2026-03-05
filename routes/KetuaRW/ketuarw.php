<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\BankSampah\DataNasabahController;
use App\Http\Controllers\Admin\BankSampah\JadwalPelaksanaanController;
use App\Http\Controllers\Admin\BankSampah\TrackingSetoranController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KetuaRW\JadwalController;
use App\Http\Controllers\Admin\KetuaRW\KelolaBankSampahController;
use App\Http\Controllers\Admin\BankSampah\DataTransaksiController;
use App\Http\Controllers\Admin\BankSampah\PencatatanController;
use App\Http\Controllers\Admin\KetuaRW\KetuaRWChatController;
use App\Http\Controllers\Admin\KetuaRW\PelaporanController;
use App\Http\Controllers\Admin\Warga\JadwalPenyetoranController;



Route::middleware(['roles:Ketua RW'])->prefix('KetuaRW')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('rw.dashboard');

    Route::controller(KelolaBankSampahController::class)
        ->group(function () {

            Route::get('/Kelola-Bank-Sampah',  'index')->name('rw.data-kelola');
            Route::get('/bank-sampah/detail/{id}',  'show')->name('rw.show-banksampah');
            Route::post('/bank-sampah/create',  'store')->name('rw.add-banksampah');
            Route::put('/bank-sampah/update/{id}',  'update')->name('rw.update-banksampah');
            Route::delete('/bank-sampah/delete/{id}',  'destroy')->name('rw.delete-banksampah');

            Route::post('/{id}/send-reminder', action: 'sendReminder')->name('banksampah.send-reminder');
        });

    Route::get('/Jadwal', [JadwalPelaksanaanController::class, 'show'])->name('rw.jadwal-pelaksanaan');
    Route::get('/jadwal-bankSampah/detail/{id}', [JadwalController::class, 'show'])->name('rw.show-jadwalBankSampah');

    Route::get('/nasabah', [DataNasabahController::class, 'index'])->name('rw.data-nasabah');
    Route::get('/tracking', [TrackingSetoranController::class, 'index'])->name('rw.data-tracking');
    Route::get('/transaksi', [DataTransaksiController::class, 'index'])->name('rw.data-transaksi');
    Route::get('/pencatatan', [PencatatanController::class, 'index'])->name('rw.pencatatan-setoran');

    Route::get('/pelaporan', [PelaporanController::class, 'index'])->name('data-pelaporanBankSampah');

    Route::post('/{id}/update-transaction', action: [PelaporanController::class, 'update'])->name('rw.open-transaction');

    Route::controller(KetuaRWChatController::class)
        ->prefix('chat')
        ->group(function () {

            Route::get('/', 'index')->name('rw.chat');
            Route::post('/create{id}', 'store')->name('rw.add-chat');
            Route::put('/update/{id}', 'update')->name('rw.update-chat');
            Route::delete('/delete/{id}', 'destroy')->name('rw.delete-chat');
            Route::delete('/deleteChat/{id}', 'deleteRoomChat')->name('rw.delete-roomChat');

            Route::put('/read{id}', 'readChat')->name('rw.read-chat');
        });

    Route::post('/chatbot/create{id}', [KetuaRWChatController::class, 'store'])->name('rw.add-chatbot');

    Route::controller(JadwalPenyetoranController::class)
        ->prefix('JanjiSetor')
        ->group(function () {

            Route::get('/', 'index')->name('rw.janji-setor');
            Route::post('/Create', 'store')->name('rw.add-janjiSetor');
            Route::put('/Update/{janjiSetor}', 'update')->name('rw.update-janjiSetor');
            Route::delete('/Delete/{janjiSetor}', 'destroy')->name('rw.delete-janjiSetor');
        });
});
