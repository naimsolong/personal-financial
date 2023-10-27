<?php

use App\Models\User;
use App\Models\Workspace;
use App\Services\WorkspaceService;

it('can change the current workspace', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $workspace->users()->attach($user->id);

    $response = $this->actingAs($user)->get('/');
    $response->assertSessionMissing('current_workspace');

    $service = new WorkspaceService();
    $service->change($workspace->id);
    
    $response = $this->actingAs($user)->get('/');
    $response->assertSessionHas('current_workspace', $workspace->id);
});