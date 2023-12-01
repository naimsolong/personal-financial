<?php

use App\Models\User;
use App\Models\Workspace;
use App\Services\WorkspaceService;

it('able to store, update and destroy workspaces table', function () {
    $user = User::factory()->create();
    $service = app(WorkspaceService::class);
    $this->actingAs($user);

    $data = collect([
        'name' => 'test'.rand(4, 10),
    ]);
    $is_created = $service->store($data);
    $this->assertDatabaseHas('workspaces', $data->toArray());
    expect($is_created)->toBeTrue();

    $model = Workspace::factory()->create();
    $data = collect([
        'name' => 'test'.rand(4, 10),
    ]);
    $is_updated = $service->update($model, $data);
    $this->assertDatabaseHas('workspaces', collect($data)->merge([
        'id' => $model->id,
    ])->toArray());
    expect($is_updated)->toBeTrue();

    $model = Workspace::factory()->create();
    $is_destroyed = $service->destroy($model);
    $this->assertModelMissing($model);
    expect($is_destroyed)->toBeTrue();
});

it('can initiate and change the current workspace', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $workspace->users()->attach($user->id);

    $response = $this->actingAs($user)->get('/');
    $response->assertSessionMissing(WorkspaceService::KEY);

    $service = new WorkspaceService();
    $service->initiate();

    $response = $this->actingAs($user)->get('/');
    $response->assertSessionHas(WorkspaceService::KEY, $workspace->id);

    $workspace = Workspace::factory()->create();
    $workspace->users()->attach($user->id);
    $service->change($workspace->id);

    $response = $this->actingAs($user)->get('/');
    $response->assertSessionHas(WorkspaceService::KEY, $workspace->id);
});

it('can attach and detach from the current workspace', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $service = app(WorkspaceService::class);
    $service->setModel($workspace);

    $service->attach($user);
    $this->assertDatabaseHas('workspace_users', [
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
    ]);

    $service->detach($user);
    $this->assertDatabaseMissing('workspace_users', [
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
    ]);
});
