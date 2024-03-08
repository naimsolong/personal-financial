<?php

use App\Models\User;
use App\Models\Waitlist;
use Inertia\Testing\AssertableInertia as Assert;

test('admin able access waitlist page', function (User $user) {
    Waitlist::factory(5)->create();

    $waitlist = Waitlist::select('email')->get()->toArray();

    $response = $this->actingAs($user)
        ->get(route('admin.waitlist.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Waitlists/Index')
            ->where('waitlists.data', $waitlist)
        );
    $response->assertOk();
})->with('admin');
