<?php

use App\Enums\TransactionsType;

it('return correct value', function () {
    $values = TransactionsType::getAllValues();

    expect($values)->toBeArray()->toBe([
        'E', 'I', 'T',
    ]);
});

it('use correct key', function () {
    $values = TransactionsType::getAllKeys();

    expect($values)->toBeArray()->toBe([
        'EXPENSE', 'INCOME', 'TRANSFER',
    ]);
});

it('return correct dropdown array', function () {
    $values = TransactionsType::dropdown();

    expect($values)->toBeArray()->toBe([
        [
            'value' => 'E',
            'text' => 'Expense',
        ],
        [
            'value' => 'I',
            'text' => 'Income',
        ],
        [
            'value' => 'T',
            'text' => 'Transfer',
        ],
    ]);
});

it('return correct descriptions', function () {
    expect(TransactionsType::EXPENSE->description())->toBeString()->toBe('Expense');
    expect(TransactionsType::INCOME->description())->toBeString()->toBe('Income');
    expect(TransactionsType::TRANSFER->description())->toBeString()->toBe('Transfer');
});
