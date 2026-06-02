<?php

use App\Http\Controllers\WelcomeController;
use App\Livewire\KaryawanAttendance;
use App\Livewire\MagangAttendance;
use App\Livewire\TamuAttendance;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/absen/karyawan', KaryawanAttendance::class)->name('absen.karyawan');
Route::get('/absen/magang', MagangAttendance::class)->name('absen.magang');
Route::get('/absen/tamu', TamuAttendance::class)->name('absen.tamu');
