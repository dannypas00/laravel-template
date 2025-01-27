<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;

it('can update self', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put(route('web.api.users.update', ['user' => $user]), [
        'name'     => 'John Doe',
        'email'    => 'test@example.com',
        'password' => Str::password(),
    ]);

    assertDatabaseHas('users', [
        'name'  => 'John Doe',
        'email' => 'test@example.com',
    ]);

    $response->assertNoContent();
});

it('can\'t update others', function (): void {
    $user = User::factory()->create();
    $self = User::factory()->create();

    $response = $this->actingAs($self)->put(route('web.api.users.update', ['user' => $user]), [
        'name'     => 'John Doe',
        'email'    => 'test@example.com',
        'password' => Str::password(),
    ]);

    $response->assertForbidden();
});
