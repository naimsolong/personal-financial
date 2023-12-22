<?php

use App\Http\Controllers\AccountGroupController;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CategoryGroupController;
use App\Http\Controllers\FiltersController;
use App\Http\Controllers\LabelsController;
use App\Http\Controllers\SchedulesController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\WorkspaceController;
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

    Route::group(['prefix' => 'workspaces'], function () {
        Route::post('/change', [WorkspaceController::class, 'change'])->name('workspaces.change');
    });

    Route::resource('workspaces', WorkspaceController::class)->except([
        'show',
    ])->names([
        'index' => 'workspaces.index',
        'create' => 'workspaces.create',
        'store' => 'workspaces.store',
        'edit' => 'workspaces.edit',
        'update' => 'workspaces.update',
        'destroy' => 'workspaces.destroy',
    ]);

    Route::group(['prefix' => 'transactions'], function () {
        Route::post('/partial', [TransactionsController::class, 'index'])->name('transactions.partial');
    });

    Route::resource('transactions', TransactionsController::class)->except([
        'show',
    ])->names([
        'index' => 'transactions.index',
        'create' => 'transactions.create',
        'store' => 'transactions.store',
        'edit' => 'transactions.edit',
        'update' => 'transactions.update',
        'destroy' => 'transactions.destroy',
    ]);

    Route::group(['prefix' => 'schedules'], function () {
        Route::get('/', [SchedulesController::class, 'index'])->name('schedules');
    });

    Route::resource('category/group', CategoryGroupController::class)->except([
        'show',
    ])->names([
        'index' => 'category.group.index',
        'create' => 'category.group.create',
        'store' => 'category.group.store',
        'edit' => 'category.group.edit',
        'update' => 'category.group.update',
        'destroy' => 'category.group.destroy',
    ]);

    Route::resource('categories', CategoriesController::class)->except([
        'show',
    ])->names([
        'index' => 'categories.index',
        'create' => 'categories.create',
        'store' => 'categories.store',
        'edit' => 'categories.edit',
        'update' => 'categories.update',
        'destroy' => 'categories.destroy',
    ]);

    Route::resource('account/group', AccountGroupController::class)->except([
        'show',
    ])->names([
        'index' => 'account.group.index',
        'create' => 'account.group.create',
        'store' => 'account.group.store',
        'edit' => 'account.group.edit',
        'update' => 'account.group.update',
        'destroy' => 'account.group.destroy',
    ]);

    Route::resource('accounts', AccountsController::class)->except([
        'show',
    ])->names([
        'index' => 'accounts.index',
        'create' => 'accounts.create',
        'store' => 'accounts.store',
        'edit' => 'accounts.edit',
        'update' => 'accounts.update',
        'destroy' => 'accounts.destroy',
    ]);

    Route::group(['prefix' => 'filters'], function () {
        Route::get('/', [FiltersController::class, 'index'])->name('filters');
    });

    Route::group(['prefix' => 'labels'], function () {
        Route::get('/', [LabelsController::class, 'index'])->name('labels');
    });
});
