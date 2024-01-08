<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/dashboard-old', function () {
    return Inertia::render('Dashboard');
})->name('dashboard-old');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard/Index');
})->name('dashboard');
