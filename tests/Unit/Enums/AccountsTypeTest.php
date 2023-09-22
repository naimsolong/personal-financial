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

it('return correct dropdown array', function () {
    $values = AccountsType::dropdown();
    
    expect($values)->toBeArray()->toBe([
        [
          "value" => "A",
          "text" => "Assets",
        ],
        [
          "value" => "L",
          "text" => "Liabilities",
        ],
    ]);
});

it('return correct descriptions', function () {
    expect(AccountsType::ASSETS->description())->toBeString()->toBe("Assets");
    expect(AccountsType::LIABILITIES->description())->toBeString()->toBe("Liabilities");
});
