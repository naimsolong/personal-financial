<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

test('user can access landing pages', function () {
    $response = $this->get(route('welcome'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('appName', config('app.name'))
            ->where('canLogin', Route::has('login'))
            ->where('canRegister', Route::has('register'))
            ->where('laravelVersion', Application::VERSION)
            ->where('phpVersion', PHP_VERSION)
        );
    $response->assertStatus(200);
});
