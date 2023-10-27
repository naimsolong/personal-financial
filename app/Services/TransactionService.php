<?php

namespace App\Services;

use App\Enums\TransactionsType;
use App\Exceptions\ServiceException;
use App\Models\Transaction;
use App\Services\Traits\TransactionOperation;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TransactionService extends BaseService
{
    use TransactionOperation;

    public function __construct()
    {
        parent::__construct(
            _class: Transaction::class
        );
    }

    public function reverseAmount(int $amount): int
    {
        return $amount * -1;
    }

    public function modifyNegativeAmount(int $amount): int
    {
        return $amount > 0 ? $amount * -1 : $amount;
    }

    public function modifyPositiveAmount(int $amount): int
    {
        return $amount < 0 ? $amount * -1 : $amount;
    }

    public function store(Collection $data): bool
    {
        $data = $data->merge(['due_at' => Carbon::createFromFormat('d/m/Y H:i', $data->get('due_date').' '.$data->get('due_time', '00:00 AM'))->format('Y-m-d H:i:s')]);

        $model = $this->getModel();

        return match($data->get('type')) {
            TransactionsType::EXPENSE->value => $this->storeExpense($model, $data),
            TransactionsType::INCOME->value => $this->storeIncome($model, $data),
            TransactionsType::TRANSFER->value => $this->storeTransfer($model, $data),
            default => throw new ServiceException('Undefined Transaction Type'),
        };
    }
    
    public function update(mixed $model, Collection $data): bool
    {
        if(is_null($model))
            throw new ServiceException('Model Not Found');

        $data = $data->merge(['due_at' => Carbon::createFromFormat('d/m/Y H:i', $data->get('due_date').' '.$data->get('due_time', '00:00 AM'))->format('Y-m-d H:i:s')]);

        return match($data->get('type')) {
            TransactionsType::EXPENSE->value => $this->updateExpense($model, $data),
            TransactionsType::INCOME->value => $this->updateIncome($model, $data),
            TransactionsType::TRANSFER->value => $this->updateTransfer($model, $data),
            default => throw new ServiceException('Undefined Transaction Type'),
        };
    }
    
    public function destroy(mixed $model): bool
    {
        if(is_null($model))
            throw new ServiceException('Model Not Found');

        return match($model->type) {
            TransactionsType::EXPENSE->value => $this->destroyExpense($model),
            TransactionsType::INCOME->value => $this->destroyIncome($model),
            TransactionsType::TRANSFER->value => $this->destroyTransfer($model),
            default => throw new ServiceException('Undefined Transaction Type'),
        };
    }
}