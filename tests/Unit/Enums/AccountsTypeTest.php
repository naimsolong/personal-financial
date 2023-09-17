<?php

use App\Enums\AccountsType;

it('return correct value', function () {
    $values = AccountsType::getAllValues();
    
    expect($values)->toBeArray()->toBe([
        'A', 'L'
    ]);
});

it('use correct key', function () {
    $values = AccountsType::getAllKeys();
    
    expect($values)->toBeArray()->toBe([
        'ASSETS', 'LIABILITIES'
    ]);
});
