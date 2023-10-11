<?php

use App\Enums\SystemCategoryCode;
use App\Enums\TransactionsType;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Sequence;

test('model class has correct properties', function () {
    expect(app(Category::class)->getFillable())->toBeArray()->toBe([
        'name',
        'code',
        'icon',
        'type',
        'only_system_flag',
    ]);
});

test('model able to perform CRUD', function () {
    $category = Category::factory()->create();
    $this->assertModelExists($category);

    $category->update([
        'name' => 'TEST',
        'code' => '000',
        'only_system_flag' => true,
    ]);
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => $category->name,
        'code' => $category->code,
        'only_system_flag' => $category->only_system_flag,
    ]);

    $category->delete();
    $this->assertSoftDeleted($category);
});

test('scope able to filter out data', function() {
    Category::whereNotNull('created_at')->delete(); // To reset table

    // TransactionsTypeFilter trait
    Category::factory()
    ->count(2)
    ->state(new Sequence(
        [ 'type' => TransactionsType::EXPENSE ],
        [ 'type' => TransactionsType::INCOME ],
    ))->create();

    expect(Category::expenseOnly()->count())->toBe(1);
    expect(Category::incomeOnly()->count())->toBe(1);
    
    Category::whereNotNull('created_at')->delete(); // To reset table

    // SystemFlagFilter trait
    Category::factory()
    ->count(2)
    ->state(new Sequence(
        [ 'only_system_flag' => true ],
        [ 'only_system_flag' => false ],
    ))->create();

    expect(Category::forUser()->count())->toBe(1);
    expect(Category::forSystem()->count())->toBe(1);
    
    Category::whereNotNull('created_at')->delete(); // To reset table

    // CategoryCodeFilter trait
    Category::factory()
    ->count(4)
    ->state(new Sequence(
        [ 'code' => SystemCategoryCode::OPENING_POSITIVE->value ],
        [ 'code' => SystemCategoryCode::OPENING_NEGATIVE->value ],
        [ 'code' => SystemCategoryCode::ADJUST_POSITIVE->value ],
        [ 'code' => SystemCategoryCode::ADJUST_NEGATIVE->value ],
    ))->create();

    expect(Category::isPositiveOpeningBalance()->count())->toBe(1);
    expect(Category::isNegativeOpeningBalance()->count())->toBe(1);
    expect(Category::isPositiveAdjustment()->count())->toBe(1);
    expect(Category::isNegativeAdjustment()->count())->toBe(1);
});