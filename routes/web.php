<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfilePreferenceController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ResearchDashboardController;
use App\Http\Controllers\SavedProjectController;
use App\Http\Controllers\UserProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/dashboard', ResearchDashboardController::class)->middleware('auth')->name('dashboard');

Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');

Route::get('/show/{project}', [ProjectController::class, 'legacyShow'])->name('projects.legacy-show');
Route::get('/upload', fn () => redirect()->route('projects.create'))->middleware('auth');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile/interests', [ProfilePreferenceController::class, 'edit'])->name('profile.preferences.edit');
    Route::put('/profile/interests', [ProfilePreferenceController::class, 'update'])->name('profile.preferences.update');

    Route::get('/projects/create', [UserProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [UserProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [UserProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [UserProjectController::class, 'update'])->name('projects.update');
    Route::post('/projects/{project}/save', [SavedProjectController::class, 'store'])->name('projects.save');
    Route::delete('/projects/{project}/save', [SavedProjectController::class, 'destroy'])->name('projects.unsave');
    Route::get('/projects/{project}/preview', [ProjectController::class, 'preview'])->name('projects.preview');
    Route::get('/projects/{project}/download', [ProjectController::class, 'download'])->name('projects.download');
});

Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

Route::middleware(['auth', 'role:super_admin,admin,moderator'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resource('projects', AdminProjectController::class)->except(['show']);

        Route::middleware('role:super_admin,admin')->group(function () {
            Route::resource('categories', CategoryController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
            Route::resource('tags', TagController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
        });

        Route::middleware('role:super_admin')->group(function () {
            Route::resource('users', UserController::class)->only(['index', 'edit', 'update']);
        });
    });
