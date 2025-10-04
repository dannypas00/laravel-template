<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('users.{id}', static fn ($user, $id) => (int) Auth::id() === (int) $id);

Broadcast::channel('jobs.{identifier}', static fn ($user, $id) => true);
