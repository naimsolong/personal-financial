<?php

use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\WaitlistController;
use Illuminate\Support\Facades\Route;

Route::prefix('/admin')->name('admin')->group(function () {
    Route::get('/', IndexController::class)->name('.index');

    Route::prefix('/waitlist')->name('.waitlist')->group(function () {
        Route::get('/', [WaitlistController::class, 'index'])->name('.index');
    });
});
