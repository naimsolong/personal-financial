<?php

use App\Enums\TransactionsType;

it('return correct value', function () {
    $values = TransactionsType::getAllValues();
    
    expect($values)->toBeArray()->toBe([
        'E', 'I', 'T'
    ]);
});

it('use correct key', function () {
    $values = TransactionsType::getAllKeys();
    
    expect($values)->toBeArray()->toBe([
        'EXPENSE', 'INCOME', 'TRANSFER'
    ]);
});
