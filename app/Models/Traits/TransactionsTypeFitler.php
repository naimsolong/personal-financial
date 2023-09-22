<?php

namespace App\Models\Traits;

use App\Enums\TransactionsType;

trait TransactionsTypeFitler {
    public function scopeExpenseOnly()
    {
        return $this->where('type', TransactionsType::EXPENSE);
    }
    
    public function scopeIncomeOnly()
    {
        return $this->where('type', TransactionsType::INCOME);
    }
}