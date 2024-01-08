<?php

use App\Http\Controllers\LabelsController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'labels'], function () {
    Route::get('/', [LabelsController::class, 'index'])->name('labels');
});