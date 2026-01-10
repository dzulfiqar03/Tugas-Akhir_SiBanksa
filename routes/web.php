<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\BankSampah\DataNasabahController;
use App\Http\Controllers\Admin\BankSampah\DataSampahController;
use App\Http\Controllers\Admin\BankSampah\DataTransaksiController;
use App\Http\Controllers\Admin\BankSampah\PencatatanController;
use App\Http\Controllers\Admin\BankSampah\TrackingSetoranController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\System\InternetConnController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/testInternet', [InternetConnController::class, 'checkConnection'])->name('check-internet');

Route::middleware(['conn'])->group(function () {

    Route::middleware(['auth'])->group(function () {

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::middleware(['verified'])->group(function () {

            Route::middleware(['roles:Ketua RW'])->group(function () {
                Route::get('/KetuaRW/dashboard', [DashboardController::class, 'index'])->name('rw.dashboard');
                Route::get('/KetuaRW/Sampah', [DataSampahController::class, 'index'])->name('rw.data-sampah');
                Route::get('/KetuaRW/nasabah', [DataNasabahController::class, 'index'])->name('rw.data-nasabah');
                Route::get('/KetuaRW/tracking', [TrackingSetoranController::class, 'index'])->name('rw.data-tracking');
                Route::get('/KetuaRW/transaksi', [DataTransaksiController::class, 'index'])->name('rw.data-transaksi');
                Route::get('/KetuaRW/pencatatan', [PencatatanController::class, 'index'])->name('rw.pencatatan-setoran');
            });

            Route::middleware(['roles:Bank Sampah'])->group(function () {
                Route::get('/Bank Sampah/dashboard', [DashboardController::class, 'index'])->name('dashboard');

                Route::get('/Bank Sampah/Sampah', [DataSampahController::class, 'index'])->name('data-sampah');
                Route::post('/Bank Sampah/Sampah/Create', [DataSampahController::class, 'store'])->name('add-sampah');
                Route::put('/Bank Sampah/Sampah/Update/{id}', [DataSampahController::class, 'update'])->name('update-sampah');
                Route::delete('/Bank Sampah/Sampah/Delete/{id}', [DataSampahController::class, 'destroy'])->name('delete-sampah');


                Route::get('/Bank Sampah/nasabah', [DataNasabahController::class, 'index'])->name('data-nasabah');
                Route::post('/bank-sampah/nasabah/create', [DataNasabahController::class, 'store'])->name('add-nasabah');
                Route::put('/bank-sampah/nasabah/update/{id}', [DataNasabahController::class, 'update'])->name('update-nasabah');
                Route::delete('/bank-sampah/nasabah/delete/{id}', [DataNasabahController::class, 'destroy'])->name('delete-nasabah');


                Route::get('/Bank Sampah/tracking', [TrackingSetoranController::class, 'index'])->name('data-tracking');
                Route::get('/Bank Sampah/transaksi', [DataTransaksiController::class, 'index'])->name('data-transaksi');
                Route::get('/Bank Sampah/pencatatan', [PencatatanController::class, 'index'])->name('pencatatan-setoran');
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
