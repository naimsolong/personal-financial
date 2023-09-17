<?php

namespace App\Enums;

use App\Enums\Concerns\ValuesFunction;

enum AccountsType: string {
    use ValuesFunction;

    case ASSETS = 'A';
    case LIABILITIES = 'L';
}