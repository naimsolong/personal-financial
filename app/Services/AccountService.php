<?php

namespace App\Services;

use App\Enums\AccountsType;
use App\Enums\TransactionsType;
use App\Exceptions\ServiceException;
use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Collection;

class AccountService extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            _class: Account::class
        );
    }

    public function store(Collection $data): bool
    {
        $accountGroup = AccountGroup::select('id', 'name', 'type')->find($data->get('account_group'));

        $model = $this->getModel()->firstOrCreate(
            ['name' => $data->get('name')],
            ['type' => $data->get('type')],
        );
        
        $accountGroup->accounts()->attach($model->id, [
            'opening_date' => $data->get('opening_date'),
            'starting_balance' => $data->get('starting_balance'),
            'latest_balance' => $data->get('starting_balance'),
            'currency' => $data->get('currency'),
            'notes' => $data->get('notes'),
        ]);

        $this->setModel($model);

        $category_id = Category::forSystem()->when($data->get('type') == AccountsType::ASSETS->value, function($query) {
            return $query->isPositiveOpeningBalance();
        }, function($query) {
            return $query->isNegativeOpeningBalance();
        })->select('id')->first()->id;

        app(TransactionService::class)->store(collect([
            'due_date' => $data->get('opening_date'),
            'due_time' => now()->format('H:i'),
            'type' => ($data->get('type') == AccountsType::ASSETS->value) ? TransactionsType::INCOME->value : TransactionsType::EXPENSE->value,
            'category' => $category_id,
            'account_from' => $model->id,
            'amount' => $data->get('starting_balance'),
            'currency' => $data->get('currency'),
            'currency_rate' => 1,
            'notes' => $data->get('notes'),
        ]));

        return !is_null($this->getModel());
    }

    public function update(mixed $model, Collection $data): bool
    {
        if(is_null($model))
            throw new ServiceException('Model Not Found');

        $accountGroup = AccountGroup::select('id', 'name', 'type')->find($data->get('account_group'));

        $is_updated = $model->update($data->only('name', 'type')->toArray());

        $model->group()->sync([
            $accountGroup->id => [
                'opening_date' => $data->get('opening_date'),
                'starting_balance' => $data->get('starting_balance'),
                'latest_balance' => $data->get('starting_balance'),
                'currency' => $data->get('currency'),
                'notes' => $data->get('notes'),
            ]
        ]);

        // TODO: Make adjustment on latest_balance

        $this->setModel($model);

        return $is_updated;
    }

    public function destroy(mixed $model): bool
    {
        if(is_null($model))
            throw new ServiceException('Model Not Found');

        $model->group()->detach();
        
        $this->setModel(null);

        // TODO: What happen to transactions

        return true;
    }

    public function updateLatestBalance(mixed $model, float $amount, float $previous_amount = 0): bool
    {
        if(is_null($model))
            throw new ServiceException('Model Not Found');

        if($amount == 0)
            throw new ServiceException('Amount cannot be zero');

        $previous_balance = $model->details->latest_balance;

        $is_updated = $model->details->update([
            'latest_balance' => $previous_balance + $amount + $previous_amount
        ]);

        $model->refresh();

        $this->setModel($model);

        return $is_updated;
    }
}