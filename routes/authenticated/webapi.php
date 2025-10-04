<?php

declare(strict_types=1);

use App\Jobs\TestJob;

Route::post('test-job', static function (Request $request) {
    $job = new TestJob($request->);
    dispatch($job);

    return $job->getIdentifier();
});
