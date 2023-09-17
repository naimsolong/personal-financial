<?php

use App\Models\Category;

test('model class has correct properties', function () {
    expect(app(Category::class)->getFillable())->toBeArray()->toBe([
        'name',
        'icon',
        'type',
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