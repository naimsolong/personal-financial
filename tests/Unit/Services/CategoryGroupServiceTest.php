<?php

use App\Enums\TransactionsType;
use App\Exceptions\ServiceException;
use App\Models\CategoryGroup;
use App\Services\CategoryGroupService;

it('able to store, update and destroy category_groups table', function() {
    $service = app(CategoryGroupService::class);

    $model = CategoryGroup::query();
    $data = collect([
        'name' => 'test'.rand(4,10),
        'type' => TransactionsType::EXPENSE->value
    ]);

    $is_created = $service->store($model, $data);
    $this->assertDatabaseHas('category_groups', $data->toArray());
    expect($is_created)->toBeTrue();
    
    $model = CategoryGroup::factory()->create();
    $data = collect([
        'name' => 'test'.rand(4,10),
        'type' => TransactionsType::INCOME->value
    ]);
    $is_updated = $service->update($model, $data);
    $this->assertDatabaseHas('category_groups', collect($data)->merge([
        'id' => $model->id
    ])->toArray());
    expect($is_updated)->toBeTrue();
    
    $model = CategoryGroup::factory()->create();
    $is_destroyed = $service->destroy($model);
    $this->assertSoftDeleted($model);
    expect($is_destroyed)->toBeTrue();
});

it('able to throw exeception if model is null', function() {
    $service = app(CategoryGroupService::class);

    expect(fn () => ($service->store(null, collect([]))))->toThrow(ServiceException::class, 'Model Not Found');
    expect(fn () => ($service->update(null, collect([]))))->toThrow(ServiceException::class, 'Model Not Found');
    expect(fn () => ($service->destroy(null)))->toThrow(ServiceException::class, 'Model Not Found');
});