<?php

use App\Http\Controllers\FiltersController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'filters'], function () {
    Route::get('/', [FiltersController::class, 'index'])->name('filters');
});
