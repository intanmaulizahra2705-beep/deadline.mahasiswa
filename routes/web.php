<?php

use App\Http\Controllers\AlgoritmaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KalenderController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\RosterController;
use App\Http\Controllers\TugasController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : view('welcome'))->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->name('login.proses');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->name('register.proses');
});

Route::middleware('auth')->group(function () {
    Route::post('/keluar', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/{kelas}', [KelasController::class, 'show'])->name('kelas.show')->whereNumber('kelas');
    Route::middleware(RoleMiddleware::class.':dosen')->group(function () {
        Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
        Route::delete('/kelas/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');
    });
    Route::middleware(RoleMiddleware::class.':mahasiswa')->group(function () {
        Route::post('/kelas/gabung', [KelasController::class, 'gabung'])->name('kelas.gabung');
        Route::post('/kelas/{kelas}/keluar', [KelasController::class, 'keluar'])->name('kelas.keluar');
    });

    // Tugas
    Route::get('/tugas', [TugasController::class, 'index'])->name('tugas.index');
    Route::get('/tugas/buat', [TugasController::class, 'create'])->name('tugas.create');
    Route::post('/tugas', [TugasController::class, 'store'])->name('tugas.store');
    Route::get('/tugas/{tugas}', [TugasController::class, 'show'])->name('tugas.show')->whereNumber('tugas');
    Route::get('/tugas/{tugas}/ubah', [TugasController::class, 'edit'])->name('tugas.edit')->whereNumber('tugas');
    Route::put('/tugas/{tugas}', [TugasController::class, 'update'])->name('tugas.update')->whereNumber('tugas');
    Route::delete('/tugas/{tugas}', [TugasController::class, 'destroy'])->name('tugas.destroy')->whereNumber('tugas');
    Route::post('/tugas/{tugas}/status', [TugasController::class, 'status'])->name('tugas.status')->whereNumber('tugas');

    // Kalender, algoritma, notifikasi, pengaturan
    Route::get('/kalender', [KalenderController::class, 'index'])->name('kalender');
    Route::get('/roster', [RosterController::class, 'index'])->name('roster');
    Route::get('/algoritma', [AlgoritmaController::class, 'index'])->name('algoritma');

    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');
    Route::post('/notifikasi/cek', [NotifikasiController::class, 'cek'])->name('notifikasi.cek');
    Route::post('/notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'baca'])->name('notifikasi.baca');

    Route::get('/pengaturan', [PengaturanController::class, 'edit'])->name('pengaturan');
    Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
});
