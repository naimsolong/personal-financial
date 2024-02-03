<?php

namespace App\Enums;

use App\Enums\Interfaces\Detail;
use App\Enums\Traits\Description;
use App\Enums\Traits\Dropdown;
use App\Enums\Traits\ValuesFunction;

enum UserRole: int implements Detail
{
    use Description, Dropdown, ValuesFunction;

    case ADMIN = 1;
    case CUSTOMER = 2;

    /**
     * Return details for this enum
     */
    public static function details(mixed $enum = null, string $key = 'description'): mixed
    {
        $details = match ($enum) {
            self::ADMIN => [
                'description' => 'Admin',
            ],
            self::CUSTOMER => [
                'description' => 'Customer',
            ],
            default => [
                'description' => 'Unknown',
            ],
        };

        return $details[$key];
    }
}
