<?php

namespace App\Enums;

use App\Enums\Concerns\Description;
use App\Enums\Concerns\Dropdown;
use App\Enums\Concerns\ValuesFunction;
use App\Enums\Interfaces\Detail;

enum TransactionsType: string implements Detail
{
    use Description, Dropdown, ValuesFunction;

    case EXPENSE = 'E';
    case INCOME = 'I';
    case TRANSFER = 'T';
    
    /**
     * Return details for this enum
     */
    public static function details(mixed $enum = null, string $key = 'description'): mixed
    {
        $details = match ($enum) {
            self::EXPENSE => [
                'description' => 'Expense',
            ],
            self::INCOME => [
                'description' => 'Income',
            ],
            self::TRANSFER => [
                'description' => 'Transfer',
            ],
            default => [
                'description' => 'Unknown',
            ],
        };

        return $details[$key];
    }
}