<?php

use App\Enums\TransactionsType;
use App\Exceptions\ServiceException;
use App\Models\Category;
use App\Models\CategoryGroup;
use App\Services\CategoryService;

it('able to store, update and destroy categories table', function() {
    $service = app(CategoryService::class);

    $model = Category::query();
    $data = collect([
        'category_group' => CategoryGroup::factory()->create()->id,
        'name' => 'test'.rand(4,10),
        'type' => TransactionsType::EXPENSE->value,
    ]);

    $is_created = $service->store($model, $data);
    $model = $service->getModel();
    $this->assertDatabaseHas('categories', $data->only('name','type')->toArray());
    $this->assertDatabaseHas('category_pivot', collect(['category_group_id' => $data->get('category_group'), 'category_id' => $model->id])->toArray());
    expect($is_created)->toBeTrue();
    
    $model = Category::factory()->create();
    $data = collect([
        'category_group' => CategoryGroup::factory()->create()->id,
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

it('able to throw exeception if model is null', function() {
    $service = app(CategoryService::class);

    expect(fn () => ($service->store(null, collect([]))))->toThrow(ServiceException::class, 'Model Not Found');
    expect(fn () => ($service->update(null, collect([]))))->toThrow(ServiceException::class, 'Model Not Found');
    expect(fn () => ($service->destroy(null)))->toThrow(ServiceException::class, 'Model Not Found');
});
