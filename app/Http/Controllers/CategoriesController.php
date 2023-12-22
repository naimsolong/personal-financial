<?php

namespace App\Http\Controllers;

use App\Enums\TransactionsType;
use App\Http\Requests\CategoryFormRequest;
use App\Models\Category;
use App\Models\CategoryGroup;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = CategoryGroup::forUser()->currentWorkspace()->select('id', 'name', 'type')->with('categories', function ($query) {
            $query->select('categories.id', 'name', 'type')->forUser()->orderBy('name');
        })->orderBy('category_groups.name')->orderBy('name')->get();

        return Inertia::render('Dashboard/Categories/Index', [
            'categories' => [
                'expense' => $categories->where('type', TransactionsType::EXPENSE->value)->toArray(),
                'income' => $categories->where('type', TransactionsType::INCOME->value)->toArray(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $categoryGroup = CategoryGroup::forUser()->currentWorkspace()->selectRaw('id AS value, name AS text, type')->get();

        return Inertia::render('Dashboard/Categories/Form', [
            'category_group' => [
                'expense' => $categoryGroup->where('type', TransactionsType::EXPENSE->value)->toArray(),
                'income' => $categoryGroup->where('type', TransactionsType::INCOME->value)->toArray(),
            ],
            'types' => collect(TransactionsType::dropdown())->whereIn('value', ['E', 'I'])->toArray(),
            'data' => [
                'id' => '',
                'name' => '',
                'category_group' => '',
                'type' => $request->query('type', ''),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryFormRequest $request)
    {
        app(CategoryService::class)->store(collect($request->only(
            'category_group',
            'name',
            'type',
        )));

        return Redirect::route('categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $categoryGroup = CategoryGroup::forUser()->currentWorkspace()->selectRaw('id AS value, name AS text, type')->get();

        return Inertia::render('Dashboard/Categories/Form', [
            'edit_mode' => true,
            'category_group' => [
                'expense' => $categoryGroup->where('type', TransactionsType::EXPENSE->value)->toArray(),
                'income' => $categoryGroup->where('type', TransactionsType::INCOME->value)->toArray(),
            ],
            'types' => collect(TransactionsType::dropdown())->whereIn('value', ['E', 'I'])->toArray(),
            'data' => collect($category->only('id', 'name', 'type'))->merge(['category_group' => $category->group()->first()->id]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryFormRequest $request, Category $category)
    {
        app(CategoryService::class)->update($category, collect($request->only(
            'category_group',
            'name',
            'type',
        )));

        return Redirect::route('categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        app(CategoryService::class)->destroy($category);

        return Redirect::route('categories.index');
    }
}
