<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\BankSampah\DataNasabahController;
use App\Http\Controllers\Admin\BankSampah\DataSampahController;
use App\Http\Controllers\Admin\BankSampah\DataTransaksiController;
use App\Http\Controllers\Admin\BankSampah\JadwalPelaksanaanController;
use App\Http\Controllers\Admin\BankSampah\KepengurusanController;
use App\Http\Controllers\Admin\BankSampah\PencatatanController;
use App\Http\Controllers\Admin\BankSampah\TrackingSetoranController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KetuaRW\JadwalController;
use App\Http\Controllers\Admin\KetuaRW\KelolaBankSampahController;
use App\Http\Controllers\Admin\PreferenceController;
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

                Route::post('/bank-sampah/{id}/send-reminder', action: [KelolaBankSampahController::class, 'sendReminder'])->name('banksampah.send-reminder');
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

                Route::get('/bank-sampah/pencatatan', [PencatatanController::class, 'index'])->name('pencatatan-setoran');
                Route::post('/bank-sampah/pencatatan/create', [PencatatanController::class, 'store'])->name('add-setoran');
            });

            Route::middleware(['roles:Warga'])->group(function () {
                Route::get('/Warga/dashboard', [DashboardController::class, 'index'])->name('warga.dashboard');
                Route::get('/Warga/transaksi', [DataTransaksiController::class, 'index'])->name('warga.data-transaksi');
                Route::get('/Warga/penjadwalan', [DataNasabahController::class, 'index'])->name('warga.penjadwalan');
                Route::get('/Warga/tracking', [TrackingSetoranController::class, 'index'])->name('warga.tracking-setoran');
            });
        });
    });

    require __DIR__ . '/auth.php';

    Route::redirect('/', 'login');
});
