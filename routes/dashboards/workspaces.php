<?php

use App\Http\Controllers\Dashboard\WorkspaceController;
use Illuminate\Support\Facades\Route;

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
