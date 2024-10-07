<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;


// タスク管理用のルート
Route::prefix('tasks')->name('tasks.')->controller(TaskController::class)->group(function () {
    Route::get('/index', 'showIndex')->name('index');
    Route::get('/store', 'showStore')->name('store');
    Route::post('/store', 'store')->name('store');
    Route::get('/update/{task}', 'showUpdate')->name('update');
    Route::put('/update/{task}', 'update')->name('update');
    Route::delete('/delete/{task}', 'delete')->name('delete');
});