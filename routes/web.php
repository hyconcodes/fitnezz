<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Role-specific dashboards
Route::middleware(['auth', 'verified', 'role:student'])->group(function () {
    Volt::route('student/dashboard', 'pages.student.dashboard')->name('student.dashboard');
});

Route::middleware(['auth', 'verified', 'role:admin,super-admin'])->group(function () {
    Volt::route('admin/dashboard', 'pages.admin.dashboard')->name('admin.dashboard');
});

Route::middleware(['auth', 'verified', 'role:trainer'])->group(function () {
    Volt::route('trainer/dashboard', 'pages.trainer.dashboard')->name('trainer.dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
});

require __DIR__.'/auth.php';
