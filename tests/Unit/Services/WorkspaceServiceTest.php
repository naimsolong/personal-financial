<?php

use App\Models\User;
use App\Models\Workspace;
use App\Services\WorkspaceService;

// TODO: Add more test

it('can initiate and change the current workspace', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $workspace->users()->attach($user->id);

    $response = $this->actingAs($user)->get('/');
    $response->assertSessionMissing('current_workspace');

    $service = new WorkspaceService();
    $service->initiate();
    
    $response = $this->actingAs($user)->get('/');
    $response->assertSessionHas('current_workspace', $workspace->id);

    $workspace = Workspace::factory()->create();
    $workspace->users()->attach($user->id);
    $service->change($workspace->id);
    
    $response = $this->actingAs($user)->get('/');
    $response->assertSessionHas('current_workspace', $workspace->id);
});