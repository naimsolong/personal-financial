<?php

namespace App\Http\Controllers;

use App\Enums\AccountsType;
use App\Http\Requests\AccountFormRequest;
use App\Models\Account;
use App\Models\AccountGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = AccountGroup::select('id', 'name', 'type')->with('accounts', function($query) {
            $query->select('accounts.id', 'name', 'type')->orderBy('name');
        })->orderBy('account_groups.name')->orderBy('name')->get();

        return Inertia::render('Dashboard/Accounts/Index', [
            'accounts' => [
                'assets' => $accounts->where('type', AccountsType::ASSETS->value)->toArray(),
                'liabilities' => $accounts->where('type', AccountsType::LIABILITIES->value)->toArray(),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $accountGroup = AccountGroup::select(DB::raw('id AS value'), DB::raw('name AS text'), 'type')->get();
        
        return Inertia::render('Dashboard/Accounts/Form', [
            'account_group' => [
                'assets' => $accountGroup->where('type', AccountsType::ASSETS->value)->toArray(),
                'liabilities' => $accountGroup->where('type', AccountsType::LIABILITIES->value)->toArray(),
            ],
            'types' => AccountsType::dropdown(),
            'data' => [
                'id' => '',
                'name' => '',
                'account_group' => '',
                'type' => $request->query('type', ''),
                'opening_date' => '',
                'starting_balance' => '0',
                'currency' => 'MYR',
                'notes' => '',
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AccountFormRequest $request)
    {
        $accountGroup = AccountGroup::find($request->account_group);

        $account = Account::create($request->only('name', 'type'));

        $accountGroup->accounts()->attach($account->id, [
            'opening_date' => $request->opening_date,
            'starting_balance' => $request->starting_balance,
            'latest_balance' => $request->starting_balance,
            'currency' => $request->currency,
            'notes' => $request->notes,
        ]);

        return Redirect::route('accounts.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        $group = $account->group()->first();

        $accountGroup = AccountGroup::select(DB::raw('id AS value'), DB::raw('name AS text'), 'type')->get();

        return Inertia::render('Dashboard/Accounts/Form', [
            'edit_mode' => true,
            'account_group' => [
                'assets' => $accountGroup->where('type', AccountsType::ASSETS->value)->toArray(),
                'liabilities' => $accountGroup->where('type', AccountsType::LIABILITIES->value)->toArray(),
            ],
            'types' => AccountsType::dropdown(),
            'data' => [
                'id' => $account->id,
                'name' => $account->name,
                'account_group' => $group->id,
                'type' => $account->type,
                'opening_date' => $group->details->opening_date,
                'starting_balance' => $group->details->starting_balance,
                'currency' => $group->details->currency,
                'notes' => $group->details->notes,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AccountFormRequest $request, Account $account)
    {
        $accountGroup = AccountGroup::find($request->account_group);

        $account->update($request->only('name', 'type'));

        $account->group()->sync([
            $accountGroup->id => [
                'opening_date' => $request->opening_date,
                'starting_balance' => $request->starting_balance,
                'latest_balance' => $request->starting_balance,
                'currency' => $request->currency,
                'notes' => $request->notes,
            ]
        ]);

        return Redirect::route('accounts.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        $account->delete();

        return Redirect::route('accounts.index');
    }
}
