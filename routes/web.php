<?php

use Illuminate\Foundation\Application;
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

include 'web/landing.php'; // landing route

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::middleware([
        'auth.role:customer',
    ])->group(function () {
        include 'web/dashboards.php'; // dashboards route
        include 'web/workspaces.php'; // workspaces route
        include 'web/transactions.php'; // transactions route
        include 'web/schedules.php'; // schedules route
        include 'web/categories.php'; // categories route
        include 'web/accounts.php'; // accounts route
        include 'web/filters.php'; // filters route
        include 'web/labels.php'; // labels route
    });

    Route::middleware([
        'auth.role:admin',
    ])->group(function () {
        include 'web/admins.php'; // admins route
    });
});
