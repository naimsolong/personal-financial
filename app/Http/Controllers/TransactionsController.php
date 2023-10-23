<?php

namespace App\Http\Controllers;

use App\Enums\TransactionsStatus;
use App\Enums\TransactionsType;
use App\Http\Requests\TransactionFormRequest;
use App\Models\AccountGroup;
use App\Models\CategoryGroup;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        $data = Transaction::currentWorkspace()->select('id', 'due_at', 'type', 'category_id', 'account_id', 'amount', 'currency', 'currency_amount', 'currency_rate', 'transfer_pair_id', 'status', 'notes')
            ->with([
                'category' => function($query) {
                    $query->select('id', 'name');
                },
                'account' => function($query) {
                    $query->select('id', 'name');
                }
            ])
            ->latest('due_at')
            ->latest('updated_at')
            ->whereRaw('WEEK(due_at) = '. now()->subWeek($request->get('next_page', 0))->format('W'))
            ->when($request->get('next_page', 0) == 0, function($query) {
                $query->orWhere('due_at', '>', now());
            })
            ->get();
        
        $return = [
            'transaction' => $data->groupBy('due_date'),
            'next_page' => $request->get('next_page', 0) + 1
        ];
        
        if($request->isMethod('post'))
            return $return;

        return Inertia::render('Dashboard/Transactions/Index', [
            'data' => $return,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $accounts = AccountGroup::selectRaw('id, name AS label, type')
            ->with([
                'accounts' => function($query) {
                    $query->selectRaw('accounts.id AS value, accounts.name AS text');
                }
            ])->orderBy('type')->get();
        $categories = CategoryGroup::forUser()->selectRaw('id, name AS label, type')
            ->with([
                'categories' => function($query) {
                    $query->selectRaw('categories.id AS value, categories.name AS text');
                }
            ])->orderBy('type')->get();
            
        return Inertia::render('Dashboard/Transactions/Form', [
            'accounts' => $accounts,
            'categories' => [
                'expense' => $categories->where('type', TransactionsType::EXPENSE->value)->toArray(),
                'income' => $categories->where('type', TransactionsType::INCOME->value)->toArray(),
            ],
            'types' => TransactionsType::dropdown(),
            'statuses' => TransactionsStatus::dropdown(),
            'data' => [
                'due_date' => now()->format('d/m/Y'),
                'due_time' => now()->format('H:i'),
                'type' => $request->query('type', 'E'),
                'category' => '',
                'account_from' => '',
                'account_to' => '',
                'amount' => '0',
                'currency' => CurrencyAlpha3::from('MYR')->value,
                'currency_rate' => 1,
                'status' => TransactionsStatus::NONE->value,
                'notes' => '',
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionFormRequest $request)
    {
        app(TransactionService::class)->store(Transaction::query(), $request->collect());
        
        return Redirect::route('transactions.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        $accounts = AccountGroup::selectRaw('id, name AS label, type')
            ->with([
                'accounts' => function($query) {
                    $query->selectRaw('accounts.id AS value, accounts.name AS text');
                }
            ])->orderBy('type')->get();
        $categories = CategoryGroup::forUser()->selectRaw('id, name AS label, type')
            ->with([
                'categories' => function($query) {
                    $query->selectRaw('categories.id AS value, categories.name AS text');
                }
            ])->orderBy('type')->get();

        $amount = app(TransactionService::class)->modifyPositiveAmount($transaction->amount);
            
        if($transaction->type == 'T') {
            $pair = $transaction->transfer_pair;

            if($transaction->amount < 0) {
                $account_id_from = $transaction->account_id;
                $account_id_to = $pair->account_id;
            } else {
                $account_id_from = $pair->account_id;
                $account_id_to = $transaction->account_id;
            }
        } else {
            $account_id_from = $transaction->account_id;
            $account_id_to = $transaction->account_id;
        }

        return Inertia::render('Dashboard/Transactions/Form', [
            'edit_mode' => true,
            'accounts' => $accounts,
            'categories' => [
                'expense' => $categories->where('type', TransactionsType::EXPENSE->value)->toArray(),
                'income' => $categories->where('type', TransactionsType::INCOME->value)->toArray(),
            ],
            'types' => TransactionsType::dropdown(),
            'statuses' => TransactionsStatus::dropdown(),
            'data' => [
                'id' => $transaction->id,
                'due_date' => $transaction->due_date,
                'due_time' => $transaction->due_time,
                'type' => $transaction->type,
                'category' => $transaction->category_id,
                'account_from' => $account_id_from,
                'account_to' => $account_id_to,
                'amount' => $amount,
                'currency' => $transaction->currency,
                'currency_rate' => $transaction->currency_rate,
                'status' => $transaction->status,
                'notes' => $transaction->notes,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TransactionFormRequest $request, Transaction $transaction)
    {
        app(TransactionService::class)->update($transaction, $request->collect());
        
        return Redirect::route('transactions.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        app(TransactionService::class)->destroy($transaction);
        
        return Redirect::route('transactions.index');
    }
}
