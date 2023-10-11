<?php

use App\Enums\AccountsType;
use App\Exceptions\ServiceException;
use App\Models\AccountGroup;
use App\Services\AccountGroupService;

it('able to store, update and destroy account_groups table', function() {
    $service = app(AccountGroupService::class);

    $model = AccountGroup::query();
    $data = collect([
        'name' => 'test'.rand(4,10),
        'type' => AccountsType::ASSETS->value
    ]);

    $is_created = $service->store($model, $data);
    $this->assertDatabaseHas('account_groups', $data->toArray());
    expect($is_created)->toBeTrue();
    
    $model = AccountGroup::factory()->create();
    $data = collect([
        'name' => 'test'.rand(4,10),
        'type' => AccountsType::LIABILITIES->value
    ]);
    $is_updated = $service->update($model, $data);
    $this->assertDatabaseHas('account_groups', collect($data)->merge([
        'id' => $model->id
    ])->toArray());
    expect($is_updated)->toBeTrue();
    
    $model = AccountGroup::factory()->create();
    $is_destroyed = $service->destroy($model);
    $this->assertSoftDeleted($model);
    expect($is_destroyed)->toBeTrue();
});

it('able to throw exeception if model is null', function() {
    $service = app(AccountGroupService::class);

    expect(fn () => ($service->store(null, collect([]))))->toThrow(ServiceException::class, 'Model Not Found');
    expect(fn () => ($service->update(null, collect([]))))->toThrow(ServiceException::class, 'Model Not Found');
    expect(fn () => ($service->destroy(null)))->toThrow(ServiceException::class, 'Model Not Found');
});