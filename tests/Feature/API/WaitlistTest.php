<?php

test('user success register as waitlist', function () {
    $email = 'test'.rand(10, 99).'@mail.com';

    $response = $this->postJson(route('join-waitlist'), [
        'email' => $email,
    ]);
    $response->assertStatus(200)->assertSee('You have been added to the waitlist!');

    $this->assertDatabaseHas('waitlists', [
        'email' => $email,
    ]);
    
    // Run again won't create new record or return validation error
    $response = $this->postJson(route('join-waitlist'), [
        'email' => $email,
    ]);
    $response->assertStatus(200)->assertSee('You have been added to the waitlist!');
});

test('spamming waitlist will hit rate limit', function() {
    // If user is spamming
    $email = 'test'.rand(10, 99).'@mail.com';
    for($i = 0; $i < 20; $i++) {
        $response = $this->postJson(route('join-waitlist'), [
            'email' => $email,
        ]);
    }
    $response->assertStatus(200)->assertSee('Too many submission sent! Try again later after 10 minutes.');

    // If bot is spamming
    $email = 'test'.rand(10, 99).'@mail.com';
    for($i = 0; $i < 100; $i++) {
        $response = $this->postJson(route('join-waitlist'), [
            'email' => $email,
        ]);
    }
    $response->assertTooManyRequests()->assertSee('Too Many Attempts.');
});