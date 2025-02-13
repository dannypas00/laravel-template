<?php

declare(strict_types=1);

namespace App\Jobs;

/**
 * @codeCoverageIgnore It's an example class (and it uses sleep)
 */
class TestJob extends BaseProgressJob
{
    private const int TICK_TIME = 1;

    public function __construct(private readonly int $runtime)
    {
        parent::__construct();
        $this->maxProgress = $this->runtime;
    }

    public function handle(): void
    {
        $this->startEvent();
        $time = 0;
        while ($time < $this->runtime) {
            sleep(self::TICK_TIME);
            $time += self::TICK_TIME;
            $this->progress(self::TICK_TIME);
        }
        $this->succeedEvent();
    }
}
