<?php

use App\Enums\TransactionsStatus;

it('return correct value', function () {
    $values = TransactionsStatus::getAllValues();

    expect($values)->toBeArray()->toBe([
        'N', 'C', 'R', 'V',
    ]);
});

it('use correct key', function () {
    $values = TransactionsStatus::getAllKeys();

    expect($values)->toBeArray()->toBe([
        'NONE', 'CLEARED', 'RECONCILED', 'VOID',
    ]);
});

it('return correct dropdown array', function () {
    $values = TransactionsStatus::dropdown();

    expect($values)->toBeArray()->toBe([
        [
            'value' => 'N',
            'text' => 'None',
        ],
        [
            'value' => 'C',
            'text' => 'Cleared',
        ],
        [
            'value' => 'R',
            'text' => 'Reconciled',
        ],
        [
            'value' => 'V',
            'text' => 'Void',
        ],
    ]);
});

it('return correct descriptions', function () {
    expect(TransactionsStatus::NONE->description())->toBeString()->toBe('None');
    expect(TransactionsStatus::CLEARED->description())->toBeString()->toBe('Cleared');
    expect(TransactionsStatus::RECONCILED->description())->toBeString()->toBe('Reconciled');
    expect(TransactionsStatus::VOID->description())->toBeString()->toBe('Void');
});
