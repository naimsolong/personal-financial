<?php

use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

test('admin able access admin page', function (User $user) {
    $response = $this->actingAs($user)
        ->get(route('admin'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Index')
        );
    $response->assertOk();
})->with('admin');
