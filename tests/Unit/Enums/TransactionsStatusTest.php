<?php

use App\Enums\TransactionsStatus;

it('return correct value', function () {
    $values = TransactionsStatus::getAllValues();
    
    expect($values)->toBeArray()->toBe([
        'N', 'C', 'R', 'V'
    ]);
});

it('use correct key', function () {
    $values = TransactionsStatus::getAllKeys();
    
    expect($values)->toBeArray()->toBe([
        'NONE', 'CLEARED', 'RECONCILED', 'VOID'
    ]);
});
