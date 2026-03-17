<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\BankSampah\ArchiverReportController;
use App\Http\Controllers\Admin\BankSampah\DataNasabahController;
use App\Http\Controllers\Admin\BankSampah\DataSampahController;
use App\Http\Controllers\Admin\BankSampah\DataTransaksiController;
use App\Http\Controllers\Admin\BankSampah\JadwalPelaksanaanController;
use App\Http\Controllers\Admin\BankSampah\JamSetorNasabahController;
use App\Http\Controllers\Admin\BankSampah\KepengurusanController;
use App\Http\Controllers\Admin\BankSampah\PencatatanController;
use App\Http\Controllers\Admin\BankSampah\TrackingSetoranController;
use App\Http\Controllers\Admin\BankSampah\UserChatController;
use App\Http\Controllers\Admin\BankSampah\DashboardController;
use App\Http\Controllers\Admin\KetuaRW\PelaporanController;
use App\Http\Controllers\DocumentArchiverController;
use App\Http\Controllers\EvidenceArchiverController;
use App\Http\Controllers\ProfileController;

Route::middleware(['roles:Bank Sampah'])->prefix('bank-sampah')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::controller(JadwalPelaksanaanController::class)
        ->prefix('Jadwal')
        ->group(function () {

            Route::get('/',  'index')->name('jadwal-pelaksanaan');
            Route::post('/Create', 'store')->name('add-jadwalBankSampah');
            Route::put('/Update/{Jadwal}',  'update')->name('update-jadwalBankSampah');
            Route::delete('/Delete/{Jadwal}',  'destroy')->name('delete-jadwalBankSampah');
        });

        Route::controller(ProfileController::class)
            ->prefix('/Dashboard')
            ->name('dashboard.')
            ->group(function () {

                Route::post('/edit', 'editAll')->name('profile-edit');
            });
    Route::controller(DataSampahController::class)
        ->prefix('Sampah')
        ->group(function () {

            Route::get('/',  'index')->name('data-sampah');
            Route::post('/Create',  'store')->name('add-sampah');
            Route::put('/Update/{id}',  'update')->name('update-sampah');
            Route::delete('/Delete/{id}', 'destroy')->name('delete-sampah');
        });

    Route::controller(DataNasabahController::class)
        ->prefix('nasabah')
        ->group(function () {

            Route::get('/',  'index')->name('data-nasabah');
            Route::get('/detail/{id}',  'show')->name('show-nasabah');
            Route::post('/create', 'store')->name('add-nasabah');
            Route::put('/update/{id}',  'update')->name('update-nasabah');
            Route::delete('/delete/{id}', 'destroy')->name('delete-nasabah');

            Route::post('/{id}/send-reminder', action: 'sendReminder')->name('nasabah.send-reminder');
        });

    Route::controller(KepengurusanController::class)
        ->prefix('kepengurusan')
        ->group(function () {

            Route::get('/',  'index')->name('data-kepengurusan');
            Route::get('/detail/{id}',  'show')->name('show-kepengurusan');
            Route::post('/create', 'store')->name('add-kepengurusan');
            Route::put('/update/{id}', 'update')->name('update-kepengurusan');
            Route::delete('/delete/{id}',  'destroy')->name('delete-kepengurusan');
        });

    Route::get('/tracking', [TrackingSetoranController::class, 'index'])->name('data-tracking');


    Route::controller(DataTransaksiController::class)
        ->prefix('transaksi')
        ->group(function () {

            Route::get('/', 'index')->name('data-transaksi');
            Route::post('/create',  'store')->name('bs.add-transaction');
            Route::put('/update/{id}',  'update')->name('bs.update-transaction');
            Route::delete('/delete/{id}', 'destroy')->name('bs.delete-transaction');
        });

    Route::controller(PencatatanController::class)
        ->prefix('pencatatan')
        ->group(function () {

            Route::get('/', 'index')->name('pencatatan-setoran');
            Route::post('/create',  'store')->name('add-setoran');
            Route::get('/detail/{id}',  'show')->name('show-pencatatan');
            Route::delete('/delete/{id}', 'destroy')->name('delete-pencatatan');
        });

    Route::get('/pelaporan', [ArchiverReportController::class, 'index'])->name('data-pelaporanRW');

    Route::controller(DocumentArchiverController::class)
        ->prefix('document')
        ->group(function () {

            Route::post('/create', 'store')->name('add-document');
            Route::put('/update/{id}',  'update')->name('update-document');
            Route::delete('/delete/{id}',  'destroy')->name('delete-document');
        });

    Route::controller(EvidenceArchiverController::class)
        ->prefix('evidence')
        ->group(function () {

            Route::post('/create',  'store')->name('add-evidence');
            Route::put('/update/{id}',  'update')->name('update-evidence');
            Route::delete('/delete/{id}',  'destroy')->name('delete-evidence');
        });

    Route::get('/setor-nasabah', [JamSetorNasabahController::class, 'index'])->name('bs.data-setor');

    Route::post('/lapor-setoran/{id}/send-reminder', action: [PelaporanController::class, 'sendReminder'])->name('laporsetoran.send-reminder');
    Route::post('/{id}/chat-transaction', action: [PelaporanController::class, 'sendChat'])->name('bs.chat-transaction');

    Route::controller(UserChatController::class)
        ->prefix('chat')
        ->group(function () {

            Route::get('/',  'index')->name('banksampah.chat');
            Route::post('/create{id}',  'store')->name('bs.add-chat');
            Route::put('/update/{id}',  'update')->name('bs.update-chat');
            Route::delete('/delete/{id}',  'destroy')->name('bs.delete-chat');
            Route::delete('/deleteChat/{id}',  'deleteRoomChat')->name('bs.delete-roomChat');

            Route::put('/read{id}',  'readChat')->name('bs.read-chat');
        });


    Route::post('/chatbot/create{id}', [UserChatController::class, 'store'])->name('bs.add-chatbot');
});
