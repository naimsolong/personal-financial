<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CategoryGroupController;
use Illuminate\Support\Facades\Route;

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