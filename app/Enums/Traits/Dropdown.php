<?php

namespace App\Enums\Traits;

use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Names;
use ArchTech\Enums\Values;

trait Dropdown
{
    use InvokableCases, Names, Values;

    /**
     * Get data for dropdown <selectbox>
     */
    public static function dropdown(): array
    {
        $names = self::names();

        return collect($names)->transform(function ($item, $key) {
            return [
                'value' => self::$item(),
                'text' => self::details(self::tryFrom(self::$item()), 'description'),
            ];
        })->toArray();
    }
}
