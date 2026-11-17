<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampusRegistrationController;
use App\Http\Controllers\CampusUserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\FacilityRequestController;
use App\Http\Controllers\LabScheduleController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleDashboardController;
use App\Http\Controllers\SuperAdminController;

// LANDING PAGE
Route::view('/', 'landing')->name('home');

// LOGIN UMUM UNTUK SEMUA ROLE
Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.process');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

// PENDAFTARAN KAMPUS
Route::get('/register/campus', [CampusRegistrationController::class, 'create'])->name('campus.register');
Route::post('/register/campus', [CampusRegistrationController::class, 'store'])->name('campus.register.store');

// DASHBOARD BERDASARKAN ROLE
Route::get('/dashboard', [RoleDashboardController::class, 'index'])->middleware('auth')->name('dashboard');

// LOGIN KHUSUS SUPER ADMIN (TETAP DIPERTAHANKAN)
Route::get('/super-admin/login', [SuperAdminController::class, 'loginForm'])->name('super-admin.login');
Route::post('/super-admin/login', [SuperAdminController::class, 'login'])->name('super-admin.login.process');

// PENGELOLAAN VERIFIKASI KAMPUS OLEH SUPER ADMIN
Route::middleware('auth')->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::patch('/campus/{id}/approve', [SuperAdminController::class, 'approve'])->name('campus.approve');
    Route::patch('/campus/{id}/reject', [SuperAdminController::class, 'reject'])->name('campus.reject');
    Route::get('/users', [SuperAdminController::class, 'users'])
        ->name('users');
    Route::patch('/users/{id}/toggle-status', [SuperAdminController::class, 'toggleUserStatus'])
        ->name('users.toggle-status');
    Route::post('/logout', [SuperAdminController::class, 'logout'])->name('logout');
});

Route::middleware('auth')
    ->prefix('campus/laboratories')
    ->name('campus.laboratories.')
    ->group(function () {
        Route::get('/', [LaboratoryController::class, 'index'])->name('index');

        Route::get('/create', [LaboratoryController::class, 'create'])->name('create');
        Route::post('/', [LaboratoryController::class, 'store'])->name('store');

        Route::get('/{id}/edit', [LaboratoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LaboratoryController::class, 'update'])->name('update');

        Route::patch('/{id}/toggle-status', [LaboratoryController::class, 'toggleStatus'])->name('toggle-status');
    });

Route::middleware('auth')
    ->prefix('campus/lab-schedules')
    ->name('campus.lab-schedules.')
    ->group(function () {
        Route::get('/', [LabScheduleController::class, 'index'])->name('index');

        Route::get('/create', [LabScheduleController::class, 'create'])->name('create');
        Route::post('/', [LabScheduleController::class, 'store'])->name('store');

        Route::get('/{id}/edit', [LabScheduleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LabScheduleController::class, 'update'])->name('update');
    });

Route::middleware('auth')
    ->prefix('campus/facility-requests')
    ->name('campus.facility-requests.')
    ->controller(FacilityRequestController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::patch('/{id}/review', 'review')->name('review');
    });

// PROFIL PENGGUNA & INFORMASI KAMPUS (semua role yang login)
Route::get('/profile', [ProfileController::class, 'show'])->middleware('auth')->name('profile.show');

// PENGELOLAAN PENGGUNA KAMPUS (ADMIN KAMPUS)
Route::middleware(['auth', 'campus.access:admin_kampus'])
    ->prefix('campus/users')
    ->name('campus.users.')
    ->controller(CampusUserController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::patch('/{id}/toggle-status', 'toggleStatus')->name('toggle-status');
    });

// KEGIATAN KAMPUS
Route::middleware(['auth', 'campus.access:admin_kampus,organisasi,dosen,mahasiswa'])
    ->prefix('campus/events')
    ->name('campus.events.')
    ->controller(EventController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/schedule', 'schedule')->name('schedule');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->whereNumber('id')->name('show');
        Route::get('/{id}/edit', 'edit')->whereNumber('id')->name('edit');
        Route::put('/{id}', 'update')->whereNumber('id')->name('update');
        Route::post('/{id}/register', 'register')->whereNumber('id')->name('register');
        Route::delete('/{id}/register', 'unregister')->whereNumber('id')->name('unregister');
    });

// PENDAFTARAN PESERTA KEGIATAN
Route::middleware(['auth', 'campus.access:admin_kampus,organisasi,dosen,mahasiswa'])
    ->prefix('campus/registrations')
    ->name('campus.registrations.')
    ->controller(EventRegistrationController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::patch('/{id}', 'update')->whereNumber('id')->name('update');
    });
