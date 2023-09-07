<?php

namespace App\Enums;

use App\Enums\Concerns\ValuesFunction;

enum TransactionsType: string {
    use ValuesFunction;

    case EXPENSE = 'E';
    case INCOME = 'I';
    case TRANSFER = 'T';
}