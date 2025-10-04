<?php

declare(strict_types=1);

Route::middleware('auth')->group(function () {
    require __DIR__ . '/authenticated/root.php';
});

require __DIR__ . '/unauthenticated/views.php';
require __DIR__ . '/auth.php';
