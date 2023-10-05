<?php

namespace App\Services\Interfaces;

interface BasicOperation
{
    public function store(mixed $model = null, array $data = []): bool;
    
    public function update(mixed $model = null, array $data = []): bool;
    
    public function destroy(mixed $model = null): bool;
}