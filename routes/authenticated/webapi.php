<?php

declare(strict_types=1);

use App\Jobs\TestJob;
use Illuminate\Http\Request;

Route::post('test-job', static function (Request $request): array {
    $duration = $request->get('duration') ?? 30;
    $job = new TestJob($duration);
    dispatch($job);

    return ['identifier' => $job->getIdentifier(), 'duration' => $duration];
})->name('test-job');
