<?php

use App\Http\Controllers\Dashboard\AccountGroupController;
use App\Http\Controllers\Dashboard\AccountsController;
use Illuminate\Support\Facades\Route;

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
