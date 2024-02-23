<?php

use App\Models\User;

test('user able to access login page', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('user able to login and redirect to dashboard', function (User $user) {
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password'
    ]);

    $response->assertRedirectToRoute('dashboard');
})->with('customers');