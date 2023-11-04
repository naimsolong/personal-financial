<?php

use App\Models\User;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use Inertia\Testing\AssertableInertia as Assert;

test('user can access workspace pages', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $workspace->users()->attach($user->id);

    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->get(route('workspaces.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Workspaces/Index')
            ->has('workspaces', 1)
        );
    $response->assertStatus(200);
    
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
    $response->assertStatus(200);
    
    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->get(route('workspaces.edit', ['workspace' => $workspace->id]))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Workspaces/Form')
            ->where('edit_mode', true)
            ->where('data', $workspace->only('id','name'))
        );
    $response->assertStatus(200);
});

test('user can perform store, update and destroy', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $workspace->users()->attach($user->id);

    $data = [
        'name' => 'test'.rand(4,10),
    ];
    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->post(route('workspaces.store'), $data);
    $response->assertRedirectToRoute('workspaces.index');
    $this->assertDatabaseHas('workspaces', $data);
    
    $workspace = Workspace::factory()->create();
    $data = [
        'name' => 'test'.rand(4,10),
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
});