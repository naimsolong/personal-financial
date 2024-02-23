<?php

use App\Http\Controllers\Admin\IndexController;
use Illuminate\Support\Facades\Route;

Route::prefix('/admin')->name('admin')->group(function() {
    Route::get('/', IndexController::class)->name('.index');
});
