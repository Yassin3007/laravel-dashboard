<?php

use App\Http\Controllers\Dashboard\PermissionController;
use App\Http\Controllers\Dashboard\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\YassinController;
use App\Http\Controllers\Dashboard\AhmedController;
use App\Http\Controllers\Dashboard\MohamedController;

Route::get('/dashboard2', function () {

    return view('dashboard.temp.index');
})->name('dashboard');



// Authentication Routes
Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Profile Routes
Route::get('/profile', [App\Http\Controllers\AuthController::class, 'profile'])->name('profile')->middleware('auth');
Route::put('/profile', [App\Http\Controllers\AuthController::class, 'updateProfile'])->name('profile.update')->middleware('auth');

// Password Reset Routes
Route::get('/forgot-password', [App\Http\Controllers\AuthController::class, 'showForgotPasswordForm'])->name('password.request')->middleware('guest');
Route::get('/reset-password', [App\Http\Controllers\AuthController::class, 'showResetPasswordForm'])->name('password.reset')->middleware('guest');















// Routes for Role
Route::middleware(['auth'])->group(function() {
    Route::get('roles', [RoleController::class, 'index'])
        ->name('roles.index')
        ->middleware('can:view_role');

    Route::get('roles/create', [RoleController::class, 'create'])
        ->name('roles.create')
        ->middleware('can:create_role');

    Route::post('roles', [RoleController::class, 'store'])
        ->name('roles.store')
        ->middleware('can:create_role');

    Route::get('roles/role', [RoleController::class, 'show'])
        ->name('roles.show')
        ->middleware('can:view_role');

    Route::get('roles/role/edit', [RoleController::class, 'edit'])
        ->name('roles.edit')
        ->middleware('can:edit_role');

    Route::put('roles/role', [RoleController::class, 'update'])
        ->name('roles.update')
        ->middleware('can:edit_role');

    Route::delete('roles/role', [RoleController::class, 'destroy'])
        ->name('roles.destroy')
        ->middleware('can:delete_role');
});

// Routes for Permission
Route::middleware(['auth'])->group(function() {
    Route::get('permissions', [PermissionController::class, 'index'])
        ->name('permissions.index')
        ->middleware('can:view_permission');

    Route::get('permissions/create', [PermissionController::class, 'create'])
        ->name('permissions.create')
        ->middleware('can:create_permission');

    Route::post('permissions', [PermissionController::class, 'store'])
        ->name('permissions.store')
        ->middleware('can:create_permission');

    Route::get('permissions/permission', [PermissionController::class, 'show'])
        ->name('permissions.show')
        ->middleware('can:view_permission');

    Route::get('permissions/permission/edit', [PermissionController::class, 'edit'])
        ->name('permissions.edit')
        ->middleware('can:edit_permission');

    Route::put('permissions/permission', [PermissionController::class, 'update'])
        ->name('permissions.update')
        ->middleware('can:edit_permission');

    Route::delete('permissions/permission', [PermissionController::class, 'destroy'])
        ->name('permissions.destroy')
        ->middleware('can:delete_permission');
});


// Routes for Yassin
Route::middleware(['auth'])->group(function() {
    Route::get('yassins', [YassinController::class, 'index'])
        ->name('yassins.index')
        ->middleware('can:view_yassin');

    Route::get('yassins/create', [YassinController::class, 'create'])
        ->name('yassins.create')
        ->middleware('can:create_yassin');

    Route::post('yassins', [YassinController::class, 'store'])
        ->name('yassins.store')
        ->middleware('can:create_yassin');

    Route::get('yassins/yassin', [YassinController::class, 'show'])
        ->name('yassins.show')
        ->middleware('can:view_yassin');

    Route::get('yassins/yassin/edit', [YassinController::class, 'edit'])
        ->name('yassins.edit')
        ->middleware('can:edit_yassin');

    Route::put('yassins/yassin', [YassinController::class, 'update'])
        ->name('yassins.update')
        ->middleware('can:edit_yassin');

    Route::delete('yassins/yassin', [YassinController::class, 'destroy'])
        ->name('yassins.destroy')
        ->middleware('can:delete_yassin');
});

// Routes for Ahmed
Route::middleware(['auth'])->group(function() {
    Route::get('ahmeds', [AhmedController::class, 'index'])
        ->name('ahmeds.index')
        ->middleware('can:view_ahmed');

    Route::get('ahmeds/create', [AhmedController::class, 'create'])
        ->name('ahmeds.create')
        ->middleware('can:create_ahmed');

    Route::post('ahmeds', [AhmedController::class, 'store'])
        ->name('ahmeds.store')
        ->middleware('can:create_ahmed');

    Route::get('ahmeds/ahmed', [AhmedController::class, 'show'])
        ->name('ahmeds.show')
        ->middleware('can:view_ahmed');

    Route::get('ahmeds/ahmed/edit', [AhmedController::class, 'edit'])
        ->name('ahmeds.edit')
        ->middleware('can:edit_ahmed');

    Route::put('ahmeds/ahmed', [AhmedController::class, 'update'])
        ->name('ahmeds.update')
        ->middleware('can:edit_ahmed');

    Route::delete('ahmeds/ahmed', [AhmedController::class, 'destroy'])
        ->name('ahmeds.destroy')
        ->middleware('can:delete_ahmed');
});

// Routes for Mohamed
Route::middleware(['auth'])->group(function() {
    Route::get('mohameds', [MohamedController::class, 'index'])
        ->name('mohameds.index')
        ->middleware('can:view_mohamed');

    Route::get('mohameds/create', [MohamedController::class, 'create'])
        ->name('mohameds.create')
        ->middleware('can:create_mohamed');

    Route::post('mohameds', [MohamedController::class, 'store'])
        ->name('mohameds.store')
        ->middleware('can:create_mohamed');

    Route::get('mohameds/mohamed', [MohamedController::class, 'show'])
        ->name('mohameds.show')
        ->middleware('can:view_mohamed');

    Route::get('mohameds/mohamed/edit', [MohamedController::class, 'edit'])
        ->name('mohameds.edit')
        ->middleware('can:edit_mohamed');

    Route::put('mohameds/mohamed', [MohamedController::class, 'update'])
        ->name('mohameds.update')
        ->middleware('can:edit_mohamed');

    Route::delete('mohameds/mohamed', [MohamedController::class, 'destroy'])
        ->name('mohameds.destroy')
        ->middleware('can:delete_mohamed');
});