<?php

use App\Http\Controllers\AccountsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CategoryGroupController;
use App\Http\Controllers\FiltersController;
use App\Http\Controllers\LabelsController;
use App\Http\Controllers\SchedulesController;
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

    Route::resource('category/group', CategoryGroupController::class)->except([
        'show'
    ])->names([
        'index' => 'category.group.index',
        'create' => 'category.group.create',
        'store' => 'category.group.store',
        'edit' => 'category.group.edit',
        'update' => 'category.group.update',
        'destroy' => 'category.group.destroy',
    ]);

    Route::resource('categories', CategoriesController::class)->except([
        'show'
    ])->names([
        'index' => 'categories.index',
        'create' => 'categories.create',
        'store' => 'categories.store',
        'edit' => 'categories.edit',
        'update' => 'categories.update',
        'destroy' => 'categories.destroy',
    ]);

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
