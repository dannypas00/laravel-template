<?php

namespace App\Jobs;

use Illuminate\Broadcasting\Channel;

class TestJob extends BaseProgressJob
{
    private const TICK_TIME = 1;

    public function __construct(private readonly string $runtime)
    {
        parent::__construct();
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
