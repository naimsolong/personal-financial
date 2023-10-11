<?php

use App\Enums\TransactionsType;
use App\Models\CategoryGroup;
use Illuminate\Database\Eloquent\Factories\Sequence;

test('model class has correct properties', function () {
    expect(app(CategoryGroup::class)->getFillable())->toBeArray()->toBe([
        'name',
        'type',
        'only_system_flag',
    ]);
});

test('model able to perform CRUD', function () {
    $categoryGroup = CategoryGroup::factory()->create();
    $this->assertModelExists($categoryGroup);

    $categoryGroup->update([
        'name' => 'TEST',
        'only_system_flag' => true,
    ]);
    $this->assertDatabaseHas('category_groups', [
        'id' => $categoryGroup->id,
        'name' => $categoryGroup->name,
        'only_system_flag' => $categoryGroup->only_system_flag,
    ]);

    $categoryGroup->delete();
    $this->assertSoftDeleted($categoryGroup);
});

test('scope able to filter out data', function() {
    CategoryGroup::whereNotNull('created_at')->delete(); // To reset table

    // TransactionsTypeFilter trait
    CategoryGroup::factory()
    ->count(2)
    ->state(new Sequence(
        [ 'type' => TransactionsType::EXPENSE ],
        [ 'type' => TransactionsType::INCOME ],
    ))->create();

    expect(CategoryGroup::expenseOnly()->count())->toBe(1);
    expect(CategoryGroup::incomeOnly()->count())->toBe(1);
    
    CategoryGroup::whereNotNull('created_at')->delete(); // To reset table

    // SystemFlagFilter trait
    CategoryGroup::factory()
    ->count(2)
    ->state(new Sequence(
        [ 'only_system_flag' => true ],
        [ 'only_system_flag' => false ],
    ))->create();

    expect(CategoryGroup::forUser()->count())->toBe(1);
    expect(CategoryGroup::forSystem()->count())->toBe(1);
});