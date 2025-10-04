<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;

Route::inertia('/', 'Welcome', [
    'canLogin'       => Route::has('login'),
    'canRegister'    => Route::has('register'),
    'laravelVersion' => Application::VERSION,
    'phpVersion'     => PHP_VERSION,
])->name('home');
