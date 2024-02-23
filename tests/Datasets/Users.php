<?php

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Workspace;

dataset('admin', [
    fn () => User::where('role', UserRole::ADMIN)->first(),
]);

dataset('customers', [
    function () {
        $user = User::factory()->create(['name' => 'test1', 'email' => 'test1@email.com']);

        $workspace = Workspace::factory()->create();

        $workspace->users()->attach($user->id);

        return $user;
    },
]);
