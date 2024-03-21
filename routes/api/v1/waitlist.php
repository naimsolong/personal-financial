<?php

use App\Http\Controllers\API\WaitlistController;
use Illuminate\Support\Facades\Route;

Route::post('/join-waitlist', [WaitlistController::class, 'join'])->name('join-waitlist');
