<?php

namespace App\Services\Interfaces;

use Illuminate\Support\Collection;

interface BasicOperation
{
    public function store(mixed $model = null, Collection $data): bool;
    
    public function update(mixed $model = null, Collection $data): bool;
    
    public function destroy(mixed $model = null): bool;
}