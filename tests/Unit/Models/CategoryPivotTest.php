<?php

use App\Models\Category;
use App\Models\CategoryGroup;
use App\Models\CategoryPivot;
use App\Models\Workspace;

test('pivot table able to attach, detach and sync', function () {
    $workspace = Workspace::factory()->create();
    $categoryGroup = CategoryGroup::factory()->create();
    $categories = Category::factory(10)->create();

    $random = $categories->random();
    $categoryGroup->categories()->attach($random->id, [
        'workspace_id' => $workspace->id,
    ]);

    expect(CategoryPivot::where(function($query) use ($categoryGroup, $random) {
        $query->where('category_group_id', $categoryGroup->id)->where('category_id', $random->id);
    })->exists())->toBeTrue();

    $categoryGroup->categories()->detach($random->id);
    expect(CategoryPivot::where(function($query) use ($categoryGroup, $random) {
        $query->where('category_group_id', $categoryGroup->id)->where('category_id', $random->id);
    })->exists())->toBeFalse();

    $categoryGroup->categories()->syncWithPivotValues($categories->pluck('id'), [
        'workspace_id' => $workspace->id,
    ]);
    expect(CategoryPivot::where(function($query) use ($categoryGroup, $categories) {
        $query->where('category_group_id', $categoryGroup->id)->whereIn('category_id', $categories->pluck('id'));
    })->exists())->toBeTrue();
    
    $categoryGroup->categories()->sync([$random->id]);
    expect(CategoryPivot::where(function($query) use ($categoryGroup, $random) {
        $query->where('category_group_id', $categoryGroup->id)->whereIn('category_id', [$random->id]);
    })->exists())->toBeTrue();
});
