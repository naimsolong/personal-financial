<?php

use App\Http\Controllers\Dashboard\SchedulesController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'schedules'], function () {
    Route::get('/', [SchedulesController::class, 'index'])->name('schedules');
});
