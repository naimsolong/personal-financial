<?php

namespace App\Models\Traits;

trait SystemFlagFilter
{
    public function scopeForUser()
    {
        return $this->where('only_system_flag', false);
    }

    public function scopeForSystem()
    {
        return $this->where('only_system_flag', true);
    }
}
