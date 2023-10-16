<?php

namespace App\Services\Traits;

use App\Enums\TransactionsStatus;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Support\Collection;

trait TransactionOperation
{
    protected function storeExpense(mixed $model = null, Collection $data): bool
    {
        $amount = $this->modifyNegativeAmount($data->get('amount'));
        
        $model = $model->create([
            'due_at' => $data->get('due_at'),
            'type' => $data->get('type'),
            'category_id' => $data->get('category'),
            'account_id' => $data->get('account_from'),
            'amount' => $amount,
            'currency' => $data->get('currency'),
            'currency_rate' => $data->get('currency_rate'),
            'status' => $data->get('status', TransactionsStatus::NONE->value),
            'notes' => $data->get('notes'),
        ]);

        $this->setModel($model);

        $this->updateAccountBalance($data->get('account_from'), $amount);

        return !is_null($this->getModel());
    }

    protected function storeIncome(mixed $model = null, Collection $data): bool
    {
        $amount = $this->modifyPositiveAmount($data->get('amount'));

        $model = $model->create([
            'due_at' => $data->get('due_at'),
            'type' => $data->get('type'),
            'category_id' => $data->get('category'),
            'account_id' => $data->get('account_from'),
            'amount' => $amount,
            'currency' => $data->get('currency'),
            'currency_rate' => $data->get('currency_rate'),
            'status' => $data->get('status', TransactionsStatus::NONE->value),
            'notes' => $data->get('notes'),
        ]);

        $this->setModel($model);

        $this->updateAccountBalance($data->get('account_from'), $amount);

        return !is_null($this->getModel());
    }

    protected function storeTransfer(mixed $model = null, Collection $data): bool
    {
        $amount_from = $this->modifyNegativeAmount($data->get('amount'));
        $amount_to = $this->modifyPositiveAmount($data->get('amount'));

        $model_from = $model->create([
            'due_at' => $data->get('due_at'),
            'type' => $data->get('type'),
            'account_id' => $data->get('account_from'),
            'amount' => $amount_from,
            'currency' => $data->get('currency'),
            'currency_rate' => $data->get('currency_rate'),
            'status' => $data->get('status', TransactionsStatus::NONE->value),
            'notes' => $data->get('notes'),
        ]);
        $model_to = $model->create([
            'due_at' => $data->get('due_at'),
            'type' => $data->get('type'),
            'account_id' => $data->get('account_to'),
            'amount' => $amount_to,
            'currency' => $data->get('currency'),
            'currency_rate' => $data->get('currency_rate'),
            'status' => $data->get('status', TransactionsStatus::NONE->value),
            'notes' => $data->get('notes'),
        ]);

        $model_from->update([
            'transfer_pair_id' => $model_to->id
        ]);
        $model_to->update([
            'transfer_pair_id' => $model_from->id
        ]);

        $model_from->refresh();
        $model_to->refresh();

        $this->setModel($model_from);

        $this->updateAccountBalance($data->get('account_from'), $amount_from);
        
        $this->updateAccountBalance($data->get('account_to'), $amount_to);

        return !is_null($this->getModel());
    }

    protected function updateExpense(mixed $model = null, Collection $data): bool
    {
        $previous_amount = $this->reverseAmount($model->amount);

        $amount = $this->modifyNegativeAmount($data->get('amount'));
        
        $model->update([
            'due_at' => $data->get('due_at'),
            'type' => $data->get('type'),
            'category_id' => $data->get('category'),
            'account_id' => $data->get('account_from'),
            'amount' => $amount,
            'currency' => $data->get('currency'),
            'currency_rate' => $data->get('currency_rate'),
            'status' => $data->get('status'),
            'notes' => $data->get('notes'),
        ]);

        $this->setModel($model);

        $this->updateAccountBalance($data->get('account_from'), $amount, $previous_amount);

        return !is_null($this->getModel());
    }

    protected function updateIncome(mixed $model = null, Collection $data): bool
    {
        $previous_amount = $this->reverseAmount($model->amount);

        $amount = $this->modifyPositiveAmount($data->get('amount'));

        $model->update([
            'due_at' => $data->get('due_at'),
            'type' => $data->get('type'),
            'category_id' => $data->get('category'),
            'account_id' => $data->get('account_from'),
            'amount' => $amount,
            'currency' => $data->get('currency'),
            'currency_rate' => $data->get('currency_rate'),
            'status' => $data->get('status'),
            'notes' => $data->get('notes'),
        ]);

        $this->setModel($model);

        $this->updateAccountBalance($data->get('account_from'), $amount, $previous_amount);

        return !is_null($this->getModel());
    }

    protected function updateTransfer(mixed $model = null, Collection $data): bool
    {
        $amount_from = $this->modifyNegativeAmount($data->get('amount'));
        $amount_to = $this->modifyPositiveAmount($data->get('amount'));

        if($model->amount < 0) {
            $previous_amount_from = $this->reverseAmount($model->amount);
            $previous_amount_to = $this->reverseAmount($model->transfer_pair->amount);

            $model_from = $model;
            $model_to = $model->transfer_pair;
        } else {
            $previous_amount_from = $this->reverseAmount($model->transfer_pair->amount);
            $previous_amount_to = $this->reverseAmount($model->amount);
            
            $model_from = $model->transfer_pair;
            $model_to = $model;
        }

        $model_from = $model_from->update([
            'due_at' => $data->get('due_at'),
            'type' => $data->get('type'),
            'amount' => $amount_from,
            'currency' => $data->get('currency'),
            'currency_rate' => $data->get('currency_rate'),
            'status' => $data->get('status'),
            'notes' => $data->get('notes'),
        ]);
        $model_to = $model_to->update([
            'due_at' => $data->get('due_at'),
            'type' => $data->get('type'),
            'amount' => $amount_to,
            'currency' => $data->get('currency'),
            'currency_rate' => $data->get('currency_rate'),
            'status' => $data->get('status'),
            'notes' => $data->get('notes'),
        ]);

        $this->setModel($model_from);

        $this->updateAccountBalance($data->get('account_from'), $amount_from, $previous_amount_from);
        
        $this->updateAccountBalance($data->get('account_to'), $amount_to, $previous_amount_to);

        return !is_null($this->getModel());
    }
    

    protected function destroyExpense(mixed $model = null): bool
    {
        $account_id = $model->account_id;

        $amount = $this->modifyPositiveAmount($model->amount);
    
        $model->delete();

        $this->setModel(null);
        
        $this->updateAccountBalance($account_id, $amount);

        return is_null($this->getModel());
    }

    protected function destroyIncome(mixed $model = null): bool
    {
        $account_id = $model->account_id;

        $amount = $this->modifyNegativeAmount($model->amount);

        $model->delete();

        $this->setModel(null);
        
        $this->updateAccountBalance($account_id, $amount);

        return is_null($this->getModel());
    }

    protected function destroyTransfer(mixed $model = null): bool
    {
        if($model->amount < 0) {
            $account_id_from = $model->account_id;
            $account_id_to = $model->transfer_pair->account_id;

            $amount_from = $this->modifyPositiveAmount($model->amount);
            $amount_to = $this->modifyNegativeAmount($model->transfer_pair->amount);
        } else {
            $account_id_from = $model->transfer_pair->account_id;
            $account_id_to = $model->account_id;

            $amount_from = $this->modifyPositiveAmount($model->transfer_pair->amount);
            $amount_to = $this->modifyNegativeAmount($model->amount);
        }

        $model->transfer_pair->delete();

        $model->delete();

        $this->setModel(null);

        $this->updateAccountBalance($account_id_from, $amount_from);
        
        $this->updateAccountBalance($account_id_to, $amount_to);

        return is_null($this->getModel());
    }

    protected function updateAccountBalance(int $account_id, float $amount, float $previous_amount = 0): bool
    {
        $account = Account::find($account_id);

        return app(AccountService::class)->updateLatestBalance($account->group()->first(), $amount, $previous_amount);        
    }
}