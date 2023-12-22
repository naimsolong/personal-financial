<?php

use App\Enums\TransactionsType;
use App\Exceptions\ServiceException;
use App\Models\CategoryGroup;
use App\Models\Workspace;
use App\Services\CategoryGroupService;
use App\Services\WorkspaceService;

it('able to store, update and destroy category_groups table', function () {
    $workspace = Workspace::factory()->create();
    app(WorkspaceService::class)->change($workspace->id);

    $service = app(CategoryGroupService::class);

    $data = collect([
        'name' => 'test'.rand(4, 10),
        'type' => TransactionsType::EXPENSE->value,
    ]);

    $is_created = $service->store($data);
    $this->assertDatabaseHas('category_groups', $data->toArray());
    expect($is_created)->toBeTrue();

    $model = CategoryGroup::factory()->create();
    $data = collect([
        'name' => 'test'.rand(4, 10),
        'type' => TransactionsType::INCOME->value,
    ]);
    $is_updated = $service->update($model, $data);
    $updated_model = $service->getModel();
    $this->assertDatabaseHas('category_groups', collect($data)->merge([
        'id' => $updated_model->id,
    ])->toArray());
    expect($is_updated)->toBeTrue();

    $model = CategoryGroup::factory()->create();
    $is_destroyed = $service->destroy($model);
    $this->assertModelExists($model);
    expect($is_destroyed)->toBeTrue();
});

it('able to throw exeception', function () {
    $service = app(CategoryGroupService::class);

    expect(fn () => ($service->update(null, collect([]))))->toThrow(ServiceException::class, 'Model Not Found');
    expect(fn () => ($service->destroy(null)))->toThrow(ServiceException::class, 'Model Not Found');
});
