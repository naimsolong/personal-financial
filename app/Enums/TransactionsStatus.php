<?php

namespace App\Enums;

use App\Enums\Concerns\ValuesFunction;

enum TransactionsStatus: string {
    use ValuesFunction;

    case NONE = 'N';
    case CLEARED = 'C';
    case RECONCILED = 'R';
    case VOID = 'V';
}