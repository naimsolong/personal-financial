<?php

namespace App\Enums;

use App\Enums\Interfaces\Detail;
use App\Enums\Traits\Description;
use App\Enums\Traits\Dropdown;
use App\Enums\Traits\ValuesFunction;

enum WaitlistStatus: string implements Detail
{
    use Description, Dropdown, ValuesFunction;

    case PENDING = 'P';
    case APPROVE = 'A';
    case REJECT = 'R';
    case IGNORE = 'I';

    /**
     * Return details for this enum
     */
    public static function details(mixed $enum = null, string $key = 'description'): mixed
    {
        $details = match ($enum) {
            self::PENDING => [
                'description' => 'Pending',
            ],
            self::APPROVE => [
                'description' => 'Approved',
            ],
            self::REJECT => [
                'description' => 'Rejected',
            ],
            self::IGNORE => [
                'description' => 'Ignored',
            ],
            default => [
                'description' => 'Unknown',
            ],
        };

        return $details[$key];
    }
}
