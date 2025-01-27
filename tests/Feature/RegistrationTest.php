<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;

test('registration screen can be rendered', function (): void {
    if (!Features::enabled(Features::registration())) {
        $this->markTestSkipped('Registration support is not enabled.');
    }

    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('registration screen cannot be rendered if support is disabled', function (): void {
    if (Features::enabled(Features::registration())) {
        $this->markTestSkipped('Registration support is enabled.');
    }

    $response = $this->get('/register');

    $response->assertStatus(404);
});

test('new users can register', function (): void {
    if (!Features::enabled(Features::registration())) {
        $this->markTestSkipped('Registration support is not enabled.');
    }

    $response = $this->post('/register', [
        'name'                  => 'Test User',
        'email'                 => 'test@example.com',
        'password'              => 'password',
        'password_confirmation' => 'password',
        'terms'                 => Jetstream::hasTermsAndPrivacyPolicyFeature(),
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(config('fortify.home'));
});
