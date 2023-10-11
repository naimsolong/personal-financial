<?php

use App\Enums\SystemCategoryCode;

it('return correct value', function () {
    $values = SystemCategoryCode::getAllValues();
    
    expect($values)->toBeArray()->toBe([
        '001','002','003','004'
    ]);
});

it('use correct key', function () {
    $values = SystemCategoryCode::getAllKeys();
    
    expect($values)->toBeArray()->toBe([
        'OPENING_POSITIVE','OPENING_NEGATIVE','ADJUST_POSITIVE','ADJUST_NEGATIVE',
    ]);
});

it('return correct dropdown array', function () {
    $values = SystemCategoryCode::dropdown();
    
    expect($values)->toBeArray()->toBe([
        [
          "value" => "001",
          "text" => "[Opening Balance (+)]",
        ],
        [
          "value" => "002",
          "text" => "[Opening Balance (-)]",
        ],
        [
          "value" => "003",
          "text" => "[Adjustment (+)]",
        ],
        [
          "value" => "004",
          "text" => "[Adjustmnet (-)]",
        ],
    ]);
});

it('return correct descriptions', function () {
    expect(SystemCategoryCode::OPENING_POSITIVE->description())->toBeString()->toBe("[Opening Balance (+)]");
    expect(SystemCategoryCode::OPENING_NEGATIVE->description())->toBeString()->toBe("[Opening Balance (-)]");
    expect(SystemCategoryCode::ADJUST_POSITIVE->description())->toBeString()->toBe("[Adjustment (+)]");
    expect(SystemCategoryCode::ADJUST_NEGATIVE->description())->toBeString()->toBe("[Adjustmnet (-)]");
});
