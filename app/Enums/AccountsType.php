<?php

namespace App\Enums;

use App\Enums\Concerns\Description;
use App\Enums\Concerns\Dropdown;
use App\Enums\Concerns\ValuesFunction;
use App\Enums\Interfaces\Detail;

enum AccountsType: string implements Detail
{
    use Description, Dropdown, ValuesFunction;

    case ASSETS = 'A';
    case LIABILITIES = 'L';
    
    /**
     * Return details for this enum
     */
    public static function details(mixed $enum = null, string $key = 'description'): mixed
    {
        $details = match ($enum) {
            self::ASSETS => [
                'description' => 'Assets',
            ],
            self::LIABILITIES => [
                'description' => 'Liabilities',
            ],
            default => [
                'description' => 'Unknown',
            ],
        };

        return $details[$key];
    }
}