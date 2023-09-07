<?php

use App\Models\Category;

test('Model class has correct properties', function () {
    expect(app(Category::class)->getFillable())->toBeArray()->toBe([
        'name',
        'icon',
        'type',
    ]);
});

test('Model able to perform CRUD', function () {
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