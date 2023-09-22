<?php

namespace App\Enums;

use App\Enums\Concerns\Description;
use App\Enums\Concerns\Dropdown;
use App\Enums\Concerns\ValuesFunction;
use App\Enums\Interfaces\Detail;

enum TransactionsStatus: string implements Detail
{
    use Description, Dropdown, ValuesFunction;

    case NONE = 'N';
    case CLEARED = 'C';
    case RECONCILED = 'R';
    case VOID = 'V';
    
    /**
     * Return details for this enum
     */
    public static function details(mixed $enum = null, string $key = 'description'): mixed
    {
        $details = match ($enum) {
            self::NONE => [
                'description' => 'None',
            ],
            self::CLEARED => [
                'description' => 'Cleared',
            ],
            self::RECONCILED => [
                'description' => 'Reconciled',
            ],
            self::VOID => [
                'description' => 'Void',
            ],
            default => [
                'description' => 'Unknown',
            ],
        };

        return $details[$key];
    }
}