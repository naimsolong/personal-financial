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
        $accountGroup = AccountGroup::currentWorkspace()->select('id', 'name', 'type')->find($data->get('account_group'));

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

        app(TransactionService::class)->storeOpeningBalance($model->id, $data);

        return !is_null($this->getModel());
    }

    public function update(mixed $model, Collection $data): bool
    {
        $this->verifyModel($model);

        $accountGroup = AccountGroup::currentWorkspace()->select('id', 'name', 'type')->find($data->get('account_group'));

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
        $this->verifyModel($model);

        $model->group()->detach();
        
        $this->setModel(null);

        // TODO: What happen to transactions

        return true;
    }

    public function updateLatestBalance(mixed $model, float $amount, float $previous_amount = 0): bool
    {
        $this->verifyModel($model);

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