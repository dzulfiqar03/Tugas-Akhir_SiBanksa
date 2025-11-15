<?php

use App\Http\Controllers\Admin\BankSampah\DataNasabahController;
use App\Http\Controllers\Admin\BankSampah\DataSampahController;
use App\Http\Controllers\Admin\BankSampah\DataTransaksiController;
use App\Http\Controllers\Admin\BankSampah\PencatatanController;
use App\Http\Controllers\Admin\BankSampah\TrackingSetoranController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::redirect('/', '/login')->name('home');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/Sampah', [DataSampahController::class, 'index'])->name('data-sampah');
Route::get('/nasabah', [DataNasabahController::class, 'index'])->name('data-nasabah');
Route::get('/tracking', [TrackingSetoranController::class, 'index'])->name('data-tracking');
Route::get('/transaksi', [DataTransaksiController::class, 'index'])->name('data-transaksi');

Route::get('/pencatatan', [PencatatanController::class, 'index'])->name('pencatatan-setoran');