<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\JobStatusEnum;
use App\Events\JobUpdatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Log;
use RateLimiter;
use Str;

class BaseProgressJob implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * Progress is always displayed a $progress / $maxProgress
     * Updating $maxProgress during runtime is supported
     */
    private int $progress = 0;

    /**
     * $maxProgress is automatically increased to
     * match $progress if it exceeds the initial value
     */
    protected int $maxProgress = 100;

    protected string $identifier;

    protected int $ratePerMinute = 20;

    public function __construct()
    {
        $this->identifier = Str::uuid()->toString();
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    protected function startEvent(): void
    {
        JobUpdatedEvent::dispatch($this->identifier, $this->progress, $this->maxProgress, JobStatusEnum::RUNNING);
    }

    protected function succeedEvent(): void
    {
        JobUpdatedEvent::dispatch($this->identifier, $this->progress, $this->maxProgress, JobStatusEnum::SUCCEEDED);
    }

    protected function progress(int $amount): void
    {
        $this->progress += $amount;

        // Max progress can never be less than progress, so make it at least match if we guess the max wrong
        if ($this->progress > $this->maxProgress) {
            $this->maxProgress = $this->progress;
        }

        Log::debug('Progressing');

        RateLimiter::attempt(
            'progress-' . $this->identifier,
            $this->ratePerMinute,
            function (): void {
                JobUpdatedEvent::dispatch($this->identifier, $this->progress, $this->maxProgress, JobStatusEnum::RUNNING);
            }
        );
    }

    public function fail($exception = null): void
    {
        JobUpdatedEvent::dispatch($this->identifier, $this->progress, $this->maxProgress, JobStatusEnum::FAILED);
    }
}
