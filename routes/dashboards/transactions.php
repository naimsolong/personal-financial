<?php

use App\Http\Controllers\Dashboard\TransactionsController;
use Illuminate\Support\Facades\Route;

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
