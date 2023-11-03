<?php

use App\Enums\TransactionsType;
use App\Exceptions\ServiceException;
use App\Models\Category;
use App\Models\CategoryGroup;
use App\Models\Workspace;
use App\Services\CategoryService;
use App\Services\WorkspaceService;

it('able to store, update and destroy categories table', function() {
    $workspace = Workspace::factory()->create();
    $categoryGroup = CategoryGroup::factory()->create();
    app(WorkspaceService::class)->change($workspace->id);
    
    $workspace->categoryGroups()->attach($categoryGroup->id);

    $service = app(CategoryService::class);

    $data = collect([
        'category_group' => $categoryGroup->id,
        'name' => 'test'.rand(4,10),
        'type' => TransactionsType::EXPENSE->value,
    ]);

    $is_created = $service->store($data);
    $model = $service->getModel();
    $this->assertDatabaseHas('categories', $data->only('name','type')->toArray());
    $this->assertDatabaseHas('category_pivot', collect(['category_group_id' => $data->get('category_group'), 'category_id' => $model->id])->toArray());
    expect($is_created)->toBeTrue();
    
    $categoryGroup = CategoryGroup::factory()->create();
    $model = Category::factory()->create();
    $workspace->categoryGroups()->attach($categoryGroup->id);
    $data = collect([
        'category_group' => $categoryGroup->id,
        'name' => 'test'.rand(4,10),
        'type' => TransactionsType::INCOME->value,
    ]);
    $is_updated = $service->update($model, $data);
    $model = $service->getModel();
    $this->assertDatabaseHas('categories', $data->only('name','type')->toArray());
    $this->assertDatabaseHas('category_pivot', collect(['category_group_id' => $data->get('category_group'), 'category_id' => $model->id])->toArray());
    expect($is_updated)->toBeTrue();
    
    $is_destroyed = $service->destroy($model);
    $this->assertDatabaseMissing('category_pivot', [
        'category_group_id' => $data->get('category_group'),
        'category_id' => $model->id
    ]);
    expect($is_destroyed)->toBeTrue();
});

it('able to throw exeception', function() {
    $service = app(CategoryService::class);

    expect(fn () => ($service->update(null, collect([]))))->toThrow(ServiceException::class, 'Model Not Found');
    expect(fn () => ($service->destroy(null)))->toThrow(ServiceException::class, 'Model Not Found');
});
