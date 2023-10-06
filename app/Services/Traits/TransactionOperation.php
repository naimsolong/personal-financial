<?php

namespace App\Services\Traits;

use Illuminate\Support\Collection;

trait TransactionOperation
{
    protected function storeExpense(mixed $model = null, Collection $data): bool
    {
        $amount = $this->modifyNegativeAmount($data->get('amount'));
        
        $model->create([
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

        return !is_null($this->getModel());
    }

    protected function storeIncome(mixed $model = null, Collection $data): bool
    {
        $amount = $this->modifyPositiveAmount($data->get('amount'));

        $model->create([
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
            'status' => $data->get('status'),
            'notes' => $data->get('notes'),
        ]);
        $model_to = $model->create([
            'due_at' => $data->get('due_at'),
            'type' => $data->get('type'),
            'account_id' => $data->get('account_to'),
            'amount' => $amount_to,
            'currency' => $data->get('currency'),
            'currency_rate' => $data->get('currency_rate'),
            'status' => $data->get('status'),
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

        return !is_null($this->getModel());
    }

    protected function updateExpense(mixed $model = null, Collection $data): bool
    {
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

        return !is_null($this->getModel());
    }

    protected function updateIncome(mixed $model = null, Collection $data): bool
    {
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

        return !is_null($this->getModel());
    }

    protected function updateTransfer(mixed $model = null, Collection $data): bool
    {

        if($model->amount < 0) {
            $amount_from = $this->modifyNegativeAmount($data->get('amount'));
            $amount_to = $this->modifyPositiveAmount($data->get('amount'));
        } else {
            $amount_from = $this->modifyPositiveAmount($data->get('amount'));
            $amount_to = $this->modifyNegativeAmount($data->get('amount'));
        }

        $model_from = $model->update([
            'due_at' => $data->get('due_at'),
            'type' => $data->get('type'),
            'amount' => $amount_from,
            'currency' => $data->get('currency'),
            'currency_rate' => $data->get('currency_rate'),
            'status' => $data->get('status'),
            'notes' => $data->get('notes'),
        ]);
        $model_to = $model->transfer_pair->update([
            'due_at' => $data->get('due_at'),
            'type' => $data->get('type'),
            'amount' => $amount_to,
            'currency' => $data->get('currency'),
            'currency_rate' => $data->get('currency_rate'),
            'status' => $data->get('status'),
            'notes' => $data->get('notes'),
        ]);

        $this->setModel($model_from);

        return !is_null($this->getModel());
    }
    

    protected function destroyExpense(mixed $model = null): bool
    {
        $model->delete();

        $this->setModel(null);

        return is_null($this->getModel());
    }

    protected function destroyIncome(mixed $model = null): bool
    {
        $model->delete();

        $this->setModel(null);

        return is_null($this->getModel());
    }

    protected function destroyTransfer(mixed $model = null): bool
    {
        $model->transfer_pair->delete();

        $model->delete();

        $this->setModel(null);

        return is_null($this->getModel());
    }
}