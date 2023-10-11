<?php

use App\Models\Category;

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
    ]);
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => $category->name,
    ]);

    $category->delete();
    $this->assertSoftDeleted($category);
});

// TODO: Perform test on relationship

// TODO: Perform test on scope