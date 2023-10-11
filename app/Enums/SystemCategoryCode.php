<?php

namespace App\Enums;

use App\Enums\Traits\Description;
use App\Enums\Traits\Dropdown;
use App\Enums\Traits\ValuesFunction;
use App\Enums\Interfaces\Detail;

enum SystemCategoryCode: string implements Detail
{
    use Description, Dropdown, ValuesFunction;

    case OPENING_POSITIVE = '001';
    case OPENING_NEGATIVE = '002';
    case ADJUST_POSITIVE = '003';
    case ADJUST_NEGATIVE = '004';
    
    /**
     * Return details for this enum
     */
    public static function details(mixed $enum = null, string $key = 'description'): mixed
    {
        $details = match ($enum) {
            self::OPENING_POSITIVE => [
                'description' => '[Opening Balance (+)]',
            ],
            self::OPENING_NEGATIVE => [
                'description' => '[Opening Balance (-)]',
            ],
            self::ADJUST_POSITIVE => [
                'description' => '[Adjustment (+)]',
            ],
            self::ADJUST_NEGATIVE => [
                'description' => '[Adjustmnet (-)]',
            ],
            default => [
                'description' => 'Unknown',
            ],
        };

        return $details[$key];
    }
}