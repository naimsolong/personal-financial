<?php

use App\Models\CategoryGroup;

test('model class has correct properties', function () {
    expect(app(CategoryGroup::class)->getFillable())->toBeArray()->toBe([
        'name',
        'type',
    ]);
});

test('model able to perform CRUD', function () {
    $categoryGroup = CategoryGroup::factory()->create();
    $this->assertModelExists($categoryGroup);

    $categoryGroup->update([
        'name' => 'TEST',
    ]);
    $this->assertDatabaseHas('category_groups', [
        'id' => $categoryGroup->id,
        'name' => $categoryGroup->name,
    ]);

    $categoryGroup->delete();
    $this->assertSoftDeleted($categoryGroup);
});