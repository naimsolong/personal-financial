<?php

namespace App\Enums\Concerns;

trait Description
{
    public function description()
    {
        return static::details($this, 'description');
    }
}