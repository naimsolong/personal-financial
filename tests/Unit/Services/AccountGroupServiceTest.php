<?php

use App\Enums\AccountsType;
use App\Exceptions\ServiceException;
use App\Models\AccountGroup;
use App\Models\Workspace;
use App\Services\AccountGroupService;
use App\Services\WorkspaceService;

it('able to store, update and destroy account_groups table', function() {
    $workspace = Workspace::factory()->create();
    app(WorkspaceService::class)->change($workspace->id);
    
    $service = app(AccountGroupService::class);

    $data = collect([
        'name' => 'test'.rand(4,10),
        'type' => AccountsType::ASSETS->value
    ]);

    $is_created = $service->store($data);
    $this->assertDatabaseHas('account_groups', $data->toArray());
    expect($is_created)->toBeTrue();
    
    $model = AccountGroup::factory()->create();
    $data = collect([
        'name' => 'test'.rand(4,10),
        'type' => AccountsType::LIABILITIES->value
    ]);
    $is_updated = $service->update($model, $data);
    $updated_model = $service->getModel();
    $this->assertDatabaseHas('account_groups', collect($data)->merge([
        'id' => $updated_model->id
    ])->toArray());
    expect($is_updated)->toBeTrue();
    
    $model = AccountGroup::factory()->create();
    $is_destroyed = $service->destroy($model);
    $this->assertModelExists($model);
    expect($is_destroyed)->toBeTrue();
});

it('able to throw exeception', function() {
    $service = app(AccountGroupService::class);

    expect(fn () => ($service->update(null, collect([]))))->toThrow(ServiceException::class, 'Model Not Found');
    expect(fn () => ($service->destroy(null)))->toThrow(ServiceException::class, 'Model Not Found');
});