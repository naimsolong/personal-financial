<?php

use App\Models\User;

test('admin able to login and redirect to dashboard', function (User $user) {
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirectToRoute('admin.index');
})->with('admin');
