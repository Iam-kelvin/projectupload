<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [ProjectController::class, 'index']);

Route::get('/upload', [ProjectController::class, 'create']);
Route::post('/projects', [ProjectController::class, 'store']);
Route::get('/show/{id}', [ProjectController::class, 'show']);

Route::get('/download-pdf/{filename}', [ProjectController::class, 'downloadPDF'])->name('download.pdf');
Route::get('/projects/download/{filename}', [ProjectController::class, 'download'])->name('projects.download');
