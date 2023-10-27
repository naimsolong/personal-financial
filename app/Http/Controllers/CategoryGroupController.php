<?php

namespace App\Http\Controllers;

use App\Enums\TransactionsType;
use App\Http\Requests\CategoryGroupFormRequest;
use App\Models\CategoryGroup;
use App\Services\CategoryGroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CategoryGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = CategoryGroup::forUser()->currentWorkspace()->select('id', 'name', 'type')->get();
        
        return Inertia::render('Dashboard/CategoryGroup/Index', [
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
        return Inertia::render('Dashboard/CategoryGroup/Form', [
            'types' => collect(TransactionsType::dropdown())->whereIn('value', ['E', 'I'])->toArray(),
            'data' => [
                'id' => '',
                'name' => '',
                'type' => $request->query('type', '')
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryGroupFormRequest $request)
    {
        app(CategoryGroupService::class)->store(collect($request->only('name', 'type')));

        return Redirect::route('category.group.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CategoryGroup $group)
    {
        return Inertia::render('Dashboard/CategoryGroup/Form', [
            'edit_mode' => true,
            'types' => collect(TransactionsType::dropdown())->whereIn('value', ['E', 'I'])->toArray(),
            'data' => $group->only('id','name','type'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryGroupFormRequest $request, CategoryGroup $group)
    {
        app(CategoryGroupService::class)->update($group, collect($request->only('name', 'type')));

        return Redirect::route('category.group.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoryGroup $group)
    {
        app(CategoryGroupService::class)->destroy($group);

        return Redirect::route('category.group.index');
    }
}
