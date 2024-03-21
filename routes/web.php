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

include 'webs/landing.php'; // landing route

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::middleware([
        'auth.role:customer',
    ])->group(function () {
        include 'dashboards/dashboards.php'; // dashboards route
        include 'dashboards/workspaces.php'; // workspaces route
        include 'dashboards/transactions.php'; // transactions route
        include 'dashboards/schedules.php'; // schedules route
        include 'dashboards/categories.php'; // categories route
        include 'dashboards/accounts.php'; // accounts route
        include 'dashboards/filters.php'; // filters route
        include 'dashboards/labels.php'; // labels route
    });

    Route::middleware([
        'auth.role:admin',
    ])->group(function () {
        include 'admins/index.php'; // admins route
    });
});
