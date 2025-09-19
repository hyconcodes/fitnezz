<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'role:super-admin', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    // Role Management Routes - Only accessible by super-admin
    Route::middleware(['role:super-admin'])->group(function () {
        // Role management using Volt component
        Volt::route('/role-management', 'admin.roles')
            ->name('role-management');
        Volt::route('/staffs-management-sys', 'admin.staffs')
            ->name('staff.sys');
    });
    // Student routes
    Route::middleware(['role:student'])->group(function () {
        Volt::route('/student-dashboard', 'students.dashboard')
            ->name('student.dashboard');
    });

    // Trainer routes
    Route::middleware(['role:trainer'])->group(function () {
        Volt::route('/trainer-dashboard', 'trainers.dashboard')
            ->name('trainer.dashboard');
    });

    // Admin routes
    Route::prefix('admin')->group(function () {
        // Students management
        Volt::route('/students', 'admin.students')
            ->middleware(['permission:view.students|create.students|edit.students|delete.students'])
            ->name('admin.students');

        // Trainers management
        Volt::route('/trainers', 'admin.trainers')
            ->middleware(['permission:view.trainers|create.trainers|edit.trainers|delete.trainers'])
            ->name('admin.trainers');

        // Admin dashboard
        Volt::route('/dashboard', 'admin.dashboard')
            ->name('admin.dashboard');
        Volt::route('/view-student', 'admin.view-student')
            ->name('admin.view-student');
        Volt::route('/equipment-sys', 'admin.equipment')
            ->middleware(['permission:view.equipment|create.equipment|edit.equipment|delete.equipment|maintain.equipment'])
            ->name('admin.equipment');
    });
});

require __DIR__ . '/auth.php';
