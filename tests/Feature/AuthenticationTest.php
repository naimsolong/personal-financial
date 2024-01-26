<?php

use App\Models\User;
use App\Models\Workspace;
use App\Providers\RouteServiceProvider;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertOk();
});

test('users can authenticate using the login screen', function () {
    $workspace = Workspace::factory()->create();
    $user = User::factory()->create();
    $workspace->users()->attach($user->id);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);
});

test('users cannot authenticate with invalid password', function () {
    $workspace = Workspace::factory()->create();
    $user = User::factory()->create();
    $workspace->users()->attach($user->id);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});
