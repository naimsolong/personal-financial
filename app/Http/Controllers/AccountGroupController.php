<?php

namespace App\Http\Controllers;

use App\Enums\AccountsType;
use App\Http\Requests\AccountGroupFormRequest;
use App\Models\AccountGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AccountGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = AccountGroup::select('id', 'name', 'type')->get();
        
        return Inertia::render('Dashboard/AccountGroup/Index', [
            'accounts' => [
                'assets' => $accounts->where('type', AccountsType::ASSETS->value)->toArray(),
                'liabilities' => $accounts->where('type', AccountsType::LIABILITIES->value)->toArray(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return Inertia::render('Dashboard/AccountGroup/Form', [
            'types' => AccountsType::dropdown(),
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
    public function store(AccountGroupFormRequest $request)
    {
        AccountGroup::create($request->only('name', 'type'));

        return Redirect::route('account.group.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AccountGroup $group)
    {
        return Inertia::render('Dashboard/AccountGroup/Form', [
            'edit_mode' => true,
            'types' => AccountsType::dropdown(),
            'data' => $group->only('id','name','type'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AccountGroupFormRequest $request, AccountGroup $group)
    {
        $group->update($request->only('name', 'type'));

        return Redirect::route('account.group.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccountGroup $group)
    {
        $group->delete();

        return Redirect::route('account.group.index');
    }
}
