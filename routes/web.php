<?php

use App\Http\Controllers\TransactionsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard-old', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard-old');

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard/Index');
    })->name('dashboard');

    Route::group(['prefix' => 'transactions'], function() {
        Route::get('/', [TransactionsController::class, 'index'])->name('transactions');
    });

    Route::group(['prefix' => 'schedules'], function() {
        Route::get('/', [SchedulesController::class, 'index'])->name('schedules');
    });

    Route::group(['prefix' => 'categories'], function() {
        Route::get('/', [CategoriesController::class, 'index'])->name('categories');
    });

    Route::group(['prefix' => 'accounts'], function() {
        Route::get('/', [AccountsController::class, 'index'])->name('accounts');
    });

    Route::group(['prefix' => 'filters'], function() {
        Route::get('/', [FiltersController::class, 'index'])->name('filters');
    });

    Route::group(['prefix' => 'labels'], function() {
        Route::get('/', [LabelsController::class, 'index'])->name('labels');
    });
});
