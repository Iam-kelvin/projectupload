<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
Route::get('/projects/{project}/preview', [ProjectController::class, 'preview'])->name('projects.preview');
Route::get('/projects/{project}/download', [ProjectController::class, 'download'])->name('projects.download');

Route::get('/show/{project}', [ProjectController::class, 'legacyShow'])->name('projects.legacy-show');
Route::get('/upload', fn () => redirect()->route('admin.projects.create'))->middleware('auth');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

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
