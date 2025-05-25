<?php

use App\Http\Controllers\Dashboard\PermissionController;
use App\Http\Controllers\Dashboard\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\PostController;


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

    Route::get('roles/{role}', [RoleController::class, 'show'])
        ->name('roles.show')
        ->middleware('can:view_role');

    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])
        ->name('roles.edit')
        ->middleware('can:edit_role');

    Route::put('roles/{role}', [RoleController::class, 'update'])
        ->name('roles.update')
        ->middleware('can:edit_role');

    Route::delete('roles/{role}', [RoleController::class, 'destroy'])
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

    Route::get('permissions/{permission}', [PermissionController::class, 'show'])
        ->name('permissions.show')
        ->middleware('can:view_permission');

    Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])
        ->name('permissions.edit')
        ->middleware('can:edit_permission');

    Route::put('permissions/{permission}', [PermissionController::class, 'update'])
        ->name('permissions.update')
        ->middleware('can:edit_permission');

    Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])
        ->name('permissions.destroy')
        ->middleware('can:delete_permission');
});





// Routes for Post
Route::middleware(['auth'])->group(function() {
    Route::get('posts', [PostController::class, 'index'])
        ->name('posts.index')
        ->middleware('can:view_post');

    Route::get('posts/create', [PostController::class, 'create'])
        ->name('posts.create')
        ->middleware('can:create_post');

    Route::post('posts', [PostController::class, 'store'])
        ->name('posts.store')
        ->middleware('can:create_post');

    Route::get('posts/{post}', [PostController::class, 'show'])
        ->name('posts.show')
        ->middleware('can:view_post');

    Route::get('posts/{post}/edit', [PostController::class, 'edit'])
        ->name('posts.edit')
        ->middleware('can:edit_post');

    Route::put('posts/{post}', [PostController::class, 'update'])
        ->name('posts.update')
        ->middleware('can:edit_post');

    Route::delete('posts/{post}', [PostController::class, 'destroy'])
        ->name('posts.destroy')
        ->middleware('can:delete_post');
});

// Routes for Post
Route::middleware(['auth'])->group(function() {
    Route::get('posts', [PostController::class, 'index'])
        ->name('posts.index')
        ->middleware('can:view_post');

    Route::get('posts/create', [PostController::class, 'create'])
        ->name('posts.create')
        ->middleware('can:create_post');

    Route::post('posts', [PostController::class, 'store'])
        ->name('posts.store')
        ->middleware('can:create_post');

    Route::get('posts/{post}', [PostController::class, 'show'])
        ->name('posts.show')
        ->middleware('can:view_post');

    Route::get('posts/{post}/edit', [PostController::class, 'edit'])
        ->name('posts.edit')
        ->middleware('can:edit_post');

    Route::put('posts/{post}', [PostController::class, 'update'])
        ->name('posts.update')
        ->middleware('can:edit_post');

    Route::delete('posts/{post}', [PostController::class, 'destroy'])
        ->name('posts.destroy')
        ->middleware('can:delete_post');
});

// Routes for Post
Route::middleware(['auth'])->group(function() {
    Route::get('posts', [PostController::class, 'index'])
        ->name('posts.index')
        ->middleware('can:view_post');

    Route::get('posts/create', [PostController::class, 'create'])
        ->name('posts.create')
        ->middleware('can:create_post');

    Route::post('posts', [PostController::class, 'store'])
        ->name('posts.store')
        ->middleware('can:create_post');

    Route::get('posts/{post}', [PostController::class, 'show'])
        ->name('posts.show')
        ->middleware('can:view_post');

    Route::get('posts/{post}/edit', [PostController::class, 'edit'])
        ->name('posts.edit')
        ->middleware('can:edit_post');

    Route::put('posts/{post}', [PostController::class, 'update'])
        ->name('posts.update')
        ->middleware('can:edit_post');

    Route::delete('posts/{post}', [PostController::class, 'destroy'])
        ->name('posts.destroy')
        ->middleware('can:delete_post');
});

// Routes for Post
Route::middleware(['auth'])->group(function() {
    Route::get('posts', [PostController::class, 'index'])
        ->name('posts.index')
        ->middleware('can:view_post');

    Route::get('posts/create', [PostController::class, 'create'])
        ->name('posts.create')
        ->middleware('can:create_post');

    Route::post('posts', [PostController::class, 'store'])
        ->name('posts.store')
        ->middleware('can:create_post');

    Route::get('posts/{post}', [PostController::class, 'show'])
        ->name('posts.show')
        ->middleware('can:view_post');

    Route::get('posts/{post}/edit', [PostController::class, 'edit'])
        ->name('posts.edit')
        ->middleware('can:edit_post');

    Route::put('posts/{post}', [PostController::class, 'update'])
        ->name('posts.update')
        ->middleware('can:edit_post');

    Route::delete('posts/{post}', [PostController::class, 'destroy'])
        ->name('posts.destroy')
        ->middleware('can:delete_post');
});

// Routes for Post
Route::middleware(['auth'])->group(function() {
    Route::get('posts', [PostController::class, 'index'])
        ->name('posts.index')
        ->middleware('can:view_post');

    Route::get('posts/create', [PostController::class, 'create'])
        ->name('posts.create')
        ->middleware('can:create_post');

    Route::post('posts', [PostController::class, 'store'])
        ->name('posts.store')
        ->middleware('can:create_post');

    Route::get('posts/{post}', [PostController::class, 'show'])
        ->name('posts.show')
        ->middleware('can:view_post');

    Route::get('posts/{post}/edit', [PostController::class, 'edit'])
        ->name('posts.edit')
        ->middleware('can:edit_post');

    Route::put('posts/{post}', [PostController::class, 'update'])
        ->name('posts.update')
        ->middleware('can:edit_post');

    Route::delete('posts/{post}', [PostController::class, 'destroy'])
        ->name('posts.destroy')
        ->middleware('can:delete_post');
});