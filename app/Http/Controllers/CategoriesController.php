<?php

namespace App\Http\Controllers;

use App\Enums\TransactionsType;
use App\Models\CategoryGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = CategoryGroup::select('id', 'name', 'type')->with('categories:id,name,type')->get();
        
        return Inertia::render('Dashboard/Categories/Index', [
            'categories' => [
                'expense' => $categories->where('type', TransactionsType::EXPENSE->value)->toArray(),
                'income' => $categories->where('type', TransactionsType::INCOME->value)->toArray(),
            ]
        ]);
    }
}
