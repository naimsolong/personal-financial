<?php

namespace App\Services\Interfaces;

use Illuminate\Support\Collection;

interface BasicOperation
{
    public function store(Collection $data): bool;
    
    public function update(mixed $model, Collection $data): bool;
    
    public function destroy(mixed $model): bool;
}