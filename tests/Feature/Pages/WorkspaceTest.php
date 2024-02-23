<?php

use App\Models\User;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use Inertia\Testing\AssertableInertia as Assert;

test('user can access workspace pages', function (User $user) {
    $workspace = $user->workspaces()->first();

    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->get(route('workspaces.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Workspaces/Index')
            ->has('workspaces', 1)
        );
    $response->assertOk();

    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->get(route('workspaces.create'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Workspaces/Form')
            ->where('data', [
                'id' => '',
                'name' => '',
            ])
        );
    $response->assertOk();

    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->get(route('workspaces.edit', ['workspace' => $workspace->id]))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Workspaces/Form')
            ->where('edit_mode', true)
            ->where('data', $workspace->only('id', 'name'))
        );
    $response->assertOk();
})->with('customers');

test('user can perform store, update and destroy', function (User $user) {
    $workspace = $user->workspaces()->first();

    $data = [
        'name' => 'test'.rand(4, 10),
    ];
    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->post(route('workspaces.store'), $data);
    $response->assertRedirectToRoute('workspaces.index');
    $this->assertDatabaseHas('workspaces', $data);

    $workspace = Workspace::factory()->create();
    $data = [
        'name' => 'test'.rand(4, 10),
    ];
    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->put(route('workspaces.update', ['workspace' => $workspace->id]), $data);
    $response->assertRedirectToRoute('workspaces.index');
    $this->assertDatabaseHas('workspaces', collect($data)->merge(['id' => $workspace->id])->toArray());

    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->delete(route('workspaces.destroy', ['workspace' => $workspace->id]));
    $response->assertRedirectToRoute('workspaces.index');
    $this->assertModelMissing($workspace);
})->with('customers');
