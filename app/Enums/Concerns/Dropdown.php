<?php

namespace App\Enums\Concerns;

use ArchTech\Enums\{
    InvokableCases, Names, Values
};

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