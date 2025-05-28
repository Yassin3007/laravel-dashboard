<?php

use App\Http\Controllers\Dashboard\PermissionController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\PostController;
use App\Http\Controllers\Dashboard\CompanyController;
use App\Http\Controllers\Dashboard\TeamController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\CategoryController;


Route::get('/dashboard2', function () {

    return view('dashboard.temp.index');
})->name('dashboard');



// Authentication Routes
Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/language/{locale}', [\App\Http\Controllers\HomeController::class, 'switchLanguage'])->name('language.switch');
// Profile Routes
Route::get('/profile', [App\Http\Controllers\AuthController::class, 'profile'])->name('profile')->middleware('auth');
Route::put('/profile', [App\Http\Controllers\AuthController::class, 'updateProfile'])->name('profile.update')->middleware('auth');

// Password Reset Routes
Route::get('/forgot-password', [App\Http\Controllers\AuthController::class, 'showForgotPasswordForm'])->name('password.request')->middleware('guest');
Route::get('/reset-password', [App\Http\Controllers\AuthController::class, 'showResetPasswordForm'])->name('password.reset')->middleware('guest');



// Profile routes (protected by auth middleware)
Route::middleware(['auth'])->group(function () {
    Route::get('/statistics', function () {

        return view('dashboard.temp.index');
    })->name('dashboard');
    // Profile edit page
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');

    // Update profile
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Delete profile image
    Route::delete('/profile/delete-image', [ProfileController::class, 'deleteImage'])->name('profile.delete-image');

    // Logout route
    Route::post('/logout', [ProfileController::class, 'logout'])->name('logout');
});











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





// Routes for Company
Route::middleware(['auth'])->group(function() {
    Route::get('companies', [CompanyController::class, 'index'])
        ->name('companies.index')
        ->middleware('can:view_company');

    Route::get('companies/create', [CompanyController::class, 'create'])
        ->name('companies.create')
        ->middleware('can:create_company');

    Route::post('companies', [CompanyController::class, 'store'])
        ->name('companies.store')
        ->middleware('can:create_company');

    Route::get('companies/{company}', [CompanyController::class, 'show'])
        ->name('companies.show')
        ->middleware('can:view_company');

    Route::get('companies/{company}/edit', [CompanyController::class, 'edit'])
        ->name('companies.edit')
        ->middleware('can:edit_company');

    Route::put('companies/{company}', [CompanyController::class, 'update'])
        ->name('companies.update')
        ->middleware('can:edit_company');

    Route::delete('companies/{company}', [CompanyController::class, 'destroy'])
        ->name('companies.destroy')
        ->middleware('can:delete_company');
});



// Routes for Team
Route::middleware(['auth'])->group(function() {
    Route::get('teams', [TeamController::class, 'index'])
        ->name('teams.index')
        ->middleware('can:view_team');

    Route::get('teams/create', [TeamController::class, 'create'])
        ->name('teams.create')
        ->middleware('can:create_team');

    Route::post('teams', [TeamController::class, 'store'])
        ->name('teams.store')
        ->middleware('can:create_team');

    Route::get('teams/{team}', [TeamController::class, 'show'])
        ->name('teams.show')
        ->middleware('can:view_team');

    Route::get('teams/{team}/edit', [TeamController::class, 'edit'])
        ->name('teams.edit')
        ->middleware('can:edit_team');

    Route::put('teams/{team}', [TeamController::class, 'update'])
        ->name('teams.update')
        ->middleware('can:edit_team');

    Route::delete('teams/{team}', [TeamController::class, 'destroy'])
        ->name('teams.destroy')
        ->middleware('can:delete_team');
});

// Routes for User
Route::middleware(['auth'])->group(function() {
    Route::get('users', [UserController::class, 'index'])
        ->name('users.index')
        ->middleware('can:view_user');

    Route::get('users/create', [UserController::class, 'create'])
        ->name('users.create')
        ->middleware('can:create_user');

    Route::post('users', [UserController::class, 'store'])
        ->name('users.store')
        ->middleware('can:create_user');

    Route::get('users/{user}', [UserController::class, 'show'])
        ->name('users.show')
        ->middleware('can:view_user');

    Route::get('users/{user}/edit', [UserController::class, 'edit'])
        ->name('users.edit')
        ->middleware('can:edit_user');

    Route::put('users/{user}', [UserController::class, 'update'])
        ->name('users.update')
        ->middleware('can:edit_user');

    Route::delete('users/{user}', [UserController::class, 'destroy'])
        ->name('users.destroy')
        ->middleware('can:delete_user');
});


// Routes for Category
Route::middleware(['auth'])->group(function() {
    Route::get('categories', [CategoryController::class, 'index'])
        ->name('categories.index')
        ->middleware('can:view_category');

    Route::get('categories/create', [CategoryController::class, 'create'])
        ->name('categories.create')
        ->middleware('can:create_category');

    Route::post('categories', [CategoryController::class, 'store'])
        ->name('categories.store')
        ->middleware('can:create_category');

    Route::get('categories/{category}', [CategoryController::class, 'show'])
        ->name('categories.show')
        ->middleware('can:view_category');

    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])
        ->name('categories.edit')
        ->middleware('can:edit_category');

    Route::put('categories/{category}', [CategoryController::class, 'update'])
        ->name('categories.update')
        ->middleware('can:edit_category');

    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])
        ->name('categories.destroy')
        ->middleware('can:delete_category');
});