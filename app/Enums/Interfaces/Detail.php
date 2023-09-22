<?php

namespace App\Enums\Interfaces;

interface Detail
{
    public static function details(mixed $enum = null, string $key = ''): mixed;
}