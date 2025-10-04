<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

test('profile information can be updated', function (): void {
    $this->actingAs($user = User::factory()->create());

    $response = $this->put('/user/profile-information', [
        'name'  => 'Test Name',
        'email' => 'test@example.com',
    ]);

    expect($user->fresh()->name)->toEqual('Test Name');
    expect($user->fresh()->email)->toEqual('test@example.com');
});
