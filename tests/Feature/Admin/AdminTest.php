<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('admin able access admin page', function (User $user) {
    $response = $this->actingAs($user)
        ->get(route('admin.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Index')
        );
    $response->assertOk();
})->with('admin');
