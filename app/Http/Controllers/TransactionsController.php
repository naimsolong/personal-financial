<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {        
        $data = Transaction::select('due_at', 'type', 'category_id', 'account_id', 'amount', 'currency', 'currency_amount', 'currency_rate', 'transfer_pair_id', 'status', 'notes')
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
}
