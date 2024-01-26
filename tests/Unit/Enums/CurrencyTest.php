<?php

use PrinsFrank\Standards\BackedEnum;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

test('have total 181 currency', function () {
    $currency = BackedEnum::toArray(CurrencyAlpha3::class);

    expect($currency)->toBeArray()->toHaveCount(181);
});

test('able to get correct Euro currency', function () {
    $currency = CurrencyAlpha3::from('EUR');

    expect($currency->value)->toBe('EUR');
    expect($currency->name)->toBe('Euro');

    expect($currency->toCurrencyNumeric()->value)->toBe('978');
    expect($currency->toCurrencyName()->value)->toBe('Euro');
    expect($currency->getSymbol()->value)->toBe('€');
    expect($currency->lowerCaseValue())->toBe('eur');
});

test('able to get correct Malaysia currency', function () {
    $currency = CurrencyAlpha3::from('MYR');

    expect($currency->value)->toBe('MYR');
    expect($currency->name)->toBe('Malaysian_Ringgit');

    expect($currency->toCurrencyNumeric()->value)->toBe('458');
    expect($currency->toCurrencyName()->value)->toBe('Malaysian Ringgit');
    expect($currency->getSymbol()->value)->toBe('RM');
    expect($currency->lowerCaseValue())->toBe('myr');
});
