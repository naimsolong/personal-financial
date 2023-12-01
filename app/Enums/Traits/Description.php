<?php

namespace App\Enums\Traits;

trait Description
{
    public function description()
    {
        return static::details($this, 'description');
    }
}
