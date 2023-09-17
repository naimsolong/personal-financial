<?php

namespace App\Enums\Concerns;

use Illuminate\Foundation\Mix;

trait ValuesFunction {
    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getAllKeys(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function getRandomValues(): mixed
    {
        return collect(self::getAllValues())->random();
    }
}