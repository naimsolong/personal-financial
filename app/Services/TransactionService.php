<?php

namespace App\Services;

use App\Enums\TransactionsType;
use App\Exceptions\ServiceException;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TransactionService extends BaseService
{
    public function modifyNegativeAmount(int $amount): int
    {
        return $amount > 0 ? $amount * -1 : $amount;
    }

    public function modifyPositiveAmount(int $amount): int
    {
        return $amount < 0 ? $amount * -1 : $amount;
    }

    public function store(mixed $model = null, Collection $data): bool
    {
        if(is_null($model))
            throw new ServiceException('Model Not Found');

        $data = $data->merge(['due_at' => Carbon::createFromFormat('d/m/Y H:i A', $data->get('due_date').' '.$data->get('due_time', '00:00 AM'))->format('Y-m-d H:i:s')]);

        return match($data->get('type')) {
            TransactionsType::EXPENSE->value => $this->storeExpense($model, $data),
            TransactionsType::INCOME->value => $this->storeIncome($model, $data),
            TransactionsType::TRANSFER->value => $this->storeTransfer($model, $data),
            default => throw new ServiceException('Undefined Transaction Type'),
        };
    }

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


}