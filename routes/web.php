<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', function () {
        return view('home');
    });
    Route::get('/home', [App\Http\Controllers\TaskController::class, 'index'])->name('home');
    Route::post('/task', [App\Http\Controllers\TaskController::class, 'store'])->name('task.store');
    Route::get('/task', [App\Http\Controllers\TaskController::class, 'listAll'])->name('task.listAll');
    Route::post('/task/{id}', [App\Http\Controllers\TaskController::class, 'update'])->name('task.update');
    Route::delete('/task/{id}', [App\Http\Controllers\TaskController::class, 'destroy'])->name('task.destroy');
    Route::get('/task/{id}', [App\Http\Controllers\TaskController::class, 'edit'])->name('task.edit');
});

